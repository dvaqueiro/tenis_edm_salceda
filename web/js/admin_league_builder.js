/*
 * Creación de ligas.
 *
 * Propone los grupos de una nueva liga a partir de la clasificación final de
 * la anterior (por defecto suben 2 y bajan 2, o 3 si el grupo tiene 7 o más)
 * y permite editarlos: arrastrando, con botones o con teclado.
 *
 * La primera parte (LeagueBuilder) son funciones puras sin DOM, para poder
 * probarlas por separado. La segunda es la interfaz.
 */
(function (root, factory) {
    if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.LeagueBuilder = factory();
    }
}(typeof self !== 'undefined' ? self : this, function () {
    'use strict';

    var LETRAS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var ORDEN_TIPO = { baja: 0, mantiene: 1, sube: 2 };

    /** "2ª grupo B" => { etiqueta: "2ª", letra: "B" }; "4ª" => { etiqueta: "4ª", letra: null } */
    function parseNombre(nombre) {
        var m = /^\s*(\d+)\s*ª\s*(?:grupo\s+([A-Za-z]))?\s*$/.exec(nombre || '');
        return m ? { etiqueta: m[1] + 'ª', letra: m[2] ? m[2].toUpperCase() : null } : null;
    }

    function nombreAuto(etiqueta, letra, total) {
        return total > 1 ? etiqueta + ' grupo ' + letra : etiqueta;
    }

    function categorias(grupos) {
        var vistas = {};
        grupos.forEach(function (g) { vistas[g.categoria] = true; });
        return Object.keys(vistas).map(Number).sort(function (a, b) { return a - b; });
    }

    function gruposDeCategoria(grupos, categoria) {
        return grupos.filter(function (g) { return g.categoria === categoria; });
    }

    /** Estructura inicial de la nueva liga: la misma que la liga anterior, sin jugadores. */
    function estructuraDesde(divisiones) {
        return divisiones.map(function (d, i) {
            var p = parseNombre(d.nombre);
            return {
                uid: 'g' + (i + 1),
                categoria: d.categoria,
                etiqueta: p ? p.etiqueta : d.categoria + 'ª',
                nombre: d.nombre,
                auto: !!p,
                jugadores: []
            };
        });
    }

    /** Vuelve a poner letras (A, B, …) y nombres automáticos a los grupos de una categoría. */
    function renombrarCategoria(grupos, categoria) {
        var enCategoria = gruposDeCategoria(grupos, categoria);
        enCategoria.forEach(function (g, i) {
            if (g.auto) {
                g.nombre = nombreAuto(g.etiqueta, LETRAS[i] || String(i + 1), enCategoria.length);
            }
        });
    }

    function limitar(sube, baja, total) {
        sube = Math.max(0, Math.min(sube, total));
        baja = Math.max(0, Math.min(baja, total - sube));
        return { sube: sube, baja: baja };
    }

    /** Qué movimientos permite la estructura: no se sube desde la categoría más alta ni se baja desde la última. */
    function limites(division, cats) {
        var i = cats.indexOf(division.categoria);
        return {
            puedeSubir: i > 0,
            puedeBajar: i >= 0 && i < cats.length - 1,
            destinoSube: i > 0 ? cats[i - 1] : null,
            destinoBaja: i >= 0 && i < cats.length - 1 ? cats[i + 1] : null
        };
    }

    /** Por defecto suben 2 y bajan 2; si el grupo tiene 7 o más, bajan 3. */
    function movimientosPorDefecto(divisiones, cats) {
        var res = {};
        divisiones.forEach(function (d) {
            var total = d.clasificacion.length;
            var l = limites(d, cats);
            res[d.id] = limitar(l.puedeSubir ? 2 : 0, l.puedeBajar ? (total >= 7 ? 3 : 2) : 0, total);
        });
        return res;
    }

    /** De dónde viene cada jugador de la liga anterior y si sube, baja o se mantiene. */
    function origenes(divisiones, movimientos) {
        var res = {};
        divisiones.forEach(function (d) {
            var total = d.clasificacion.length;
            var m = movimientos[d.id] || { sube: 0, baja: 0 };
            d.clasificacion.forEach(function (fila, i) {
                var tipo = i < m.sube ? 'sube' : (i >= total - m.baja ? 'baja' : 'mantiene');
                var p = parseNombre(d.nombre);
                res[fila.id] = {
                    division: d.id,
                    nombre: d.nombre,
                    corto: p ? p.etiqueta + (p.letra ? ' ' + p.letra : '') : d.nombre,
                    categoria: d.categoria,
                    posicion: fila.posicion,
                    total: total,
                    indice: i,
                    tipo: tipo
                };
            });
        });
        return res;
    }

    /** Orden "natural" dentro de un grupo: primero los que vienen de arriba y por su puesto. */
    function compararSemilla(origen) {
        return function (a, b) {
            var oa = origen[a], ob = origen[b];
            if (!oa || !ob) {
                return (oa ? 0 : 1) - (ob ? 0 : 1);
            }
            return (oa.categoria - ob.categoria) ||
                (ORDEN_TIPO[oa.tipo] - ORDEN_TIPO[ob.tipo]) ||
                (oa.posicion - ob.posicion) ||
                (oa.nombre < ob.nombre ? -1 : oa.nombre > ob.nombre ? 1 : 0);
        };
    }

    /**
     * Índice de la letra de cada grupo de la liga anterior dentro de su
     * categoría (A = 0, B = 1…). Si el nombre no lleva letra se usa su orden.
     */
    function letrasDeOrigen(divisiones) {
        var res = {};
        var vistos = {};
        divisiones.forEach(function (d) {
            var p = parseNombre(d.nombre);
            vistos[d.categoria] = (vistos[d.categoria] || 0) + 1;
            res[d.id] = p && p.letra ? LETRAS.indexOf(p.letra) : vistos[d.categoria] - 1;
        });
        return res;
    }

    /**
     * Rellena los grupos según el artículo 11 del reglamento. Llamamos "misma
     * letra" al grupo de destino con la letra del grupo de origen y "contraria"
     * a la siguiente:
     *  - Suben: el 1º a la misma letra, el 2º a la contraria.
     *  - Bajan: el 5º a la misma letra, el 6º a la contraria y el 7º a la misma
     *    (cuenta el puesto: si baja el 4º, va a la contraria).
     *  - Se mantienen en la categoría más alta: impares al grupo A y pares al B.
     *  - Se mantienen en el resto: impares (3º) en su grupo, pares (4º) al contrario.
     * Si la categoría de destino tiene un solo grupo, van todos a él.
     *
     * @return {{grupos: Array, sinGrupo: number[]}} copia de `grupos` con jugadores
     */
    function proponer(divisiones, grupos, movimientos) {
        var cats = categorias(grupos);
        var nuevos = grupos.map(function (g) {
            return Object.assign({}, g, { jugadores: [] });
        });
        var sinGrupo = [];
        var colocados = {};
        var origen = origenes(divisiones, movimientos);
        var letras = letrasDeOrigen(divisiones);

        divisiones.forEach(function (d) {
            var total = d.clasificacion.length;
            var m = movimientos[d.id] || { sube: 0, baja: 0 };
            var i = cats.indexOf(d.categoria);
            var misma = letras[d.id];

            d.clasificacion.forEach(function (fila, pos) {
                if (colocados[fila.id]) {
                    return;
                }
                colocados[fila.id] = true;

                var destino, letra;
                if (pos < m.sube) {
                    destino = i > 0 ? cats[i - 1] : null;
                    letra = misma + pos % 2;
                } else if (pos >= total - m.baja) {
                    destino = i >= 0 && i < cats.length - 1 ? cats[i + 1] : null;
                    letra = misma + pos % 2;
                } else {
                    destino = i >= 0 ? d.categoria : null;
                    letra = i === 0 ? pos % 2 : misma + pos % 2;
                }

                if (destino === null) {
                    sinGrupo.push(fila.id);
                    return;
                }
                var candidatos = gruposDeCategoria(nuevos, destino);
                candidatos[letra % candidatos.length].jugadores.push(fila.id);
            });
        });

        nuevos.forEach(function (g) {
            g.jugadores.sort(compararSemilla(origen));
        });

        return { grupos: nuevos, sinGrupo: sinGrupo };
    }

    /** Reparte alternando (A, B, A, B…) a todos los jugadores de una categoría según su procedencia. */
    function repartir(grupos, categoria, origen) {
        var enCategoria = gruposDeCategoria(grupos, categoria);
        var todos = [];
        enCategoria.forEach(function (g) { todos = todos.concat(g.jugadores); });
        todos.sort(compararSemilla(origen));
        enCategoria.forEach(function (g) { g.jugadores = []; });
        todos.forEach(function (id, i) {
            enCategoria[i % enCategoria.length].jugadores.push(id);
        });
    }

    /** Busca sin distinguir mayúsculas ni tildes. */
    function normalizar(texto) {
        return String(texto || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();
    }

    return {
        parseNombre: parseNombre,
        nombreAuto: nombreAuto,
        categorias: categorias,
        gruposDeCategoria: gruposDeCategoria,
        estructuraDesde: estructuraDesde,
        renombrarCategoria: renombrarCategoria,
        limitar: limitar,
        limites: limites,
        movimientosPorDefecto: movimientosPorDefecto,
        origenes: origenes,
        compararSemilla: compararSemilla,
        proponer: proponer,
        repartir: repartir,
        normalizar: normalizar
    };
}));

/* ------------------------------------------------------------------------ */
/* Interfaz                                                                  */
/* ------------------------------------------------------------------------ */
(function () {
    'use strict';

    if (typeof document === 'undefined' || !document.getElementById('lb')) {
        return;
    }

    var LB = window.LeagueBuilder;
    var CONFIG = window.LB_CONFIG;
    var DATA = JSON.parse(document.getElementById('lb-data').textContent);
    var DIVISIONES = DATA.divisiones;
    var DRAFT_KEY = 'lb-borrador:' + DATA.base.id;
    var MIN_JUGADORES = 4;
    var MAX_JUGADORES = 7;
    var REDUCIR_MOVIMIENTO = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var JUGADORES = {};
    DATA.jugadores.forEach(function (j) { JUGADORES[j.id] = j; });
    // Quien jugó la liga anterior existe aunque ya no esté en la lista general.
    DIVISIONES.forEach(function (d) {
        d.clasificacion.forEach(function (f) {
            if (!JUGADORES[f.id]) {
                JUGADORES[f.id] = { id: f.id, nombre: f.nombre, activo: true };
            }
        });
    });

    var state;
    var historial = [];
    var rehacer = [];
    var sortables = [];
    var addTarget = null;
    var renameTarget = null;
    var moveTarget = null;
    var guardando = false;

    /* ---------- utilidades DOM ---------- */

    function h(tag, attrs) {
        var el = document.createElement(tag);
        var hijos = Array.prototype.slice.call(arguments, 2);
        Object.keys(attrs || {}).forEach(function (k) {
            var v = attrs[k];
            if (v === null || v === undefined || v === false) {
                return;
            }
            if (k === 'class') {
                el.className = v;
            } else if (k === 'text') {
                el.textContent = v;
            } else if (k.indexOf('on') === 0) {
                el.addEventListener(k.substring(2), v);
            } else if (v === true) {
                el.setAttribute(k, '');
            } else {
                el.setAttribute(k, v);
            }
        });
        hijos.forEach(function add(hijo) {
            if (hijo === null || hijo === undefined || hijo === false) {
                return;
            }
            if (Array.isArray(hijo)) {
                hijo.forEach(add);
            } else {
                el.appendChild(typeof hijo === 'string' ? document.createTextNode(hijo) : hijo);
            }
        });
        return el;
    }

    function icono(nombre) {
        return h('i', { class: 'material-icons', 'aria-hidden': 'true' }, nombre);
    }

    function srOnly(texto) {
        return h('span', { class: 'sr-only' }, texto);
    }

    function byId(id) {
        return document.getElementById(id);
    }

    function anunciar(texto) {
        var live = byId('lb-live');
        live.textContent = '';
        // Pequeño retardo para que los lectores de pantalla detecten el cambio aunque el texto se repita.
        setTimeout(function () { live.textContent = texto; }, 60);
    }

    function plural(n, uno, varios) {
        return n + ' ' + (n === 1 ? uno : varios);
    }

    function nombreJugador(id) {
        return JUGADORES[id] ? JUGADORES[id].nombre : 'Jugador ' + id;
    }

    /* ---------- estado ---------- */

    function estadoInicial() {
        var estructura = LB.estructuraDesde(DIVISIONES);
        var movimientos = LB.movimientosPorDefecto(DIVISIONES, LB.categorias(estructura));
        var propuesta = LB.proponer(DIVISIONES, estructura, movimientos);
        return {
            nombre: DATA.nombreSugerido || '',
            grupos: propuesta.grupos,
            sinGrupo: propuesta.sinGrupo,
            movimientos: movimientos,
            siguienteUid: estructura.length + 1
        };
    }

    function origenActual() {
        return LB.origenes(DIVISIONES, state.movimientos);
    }

    function grupoPorUid(uid) {
        for (var i = 0; i < state.grupos.length; i++) {
            if (state.grupos[i].uid === uid) {
                return state.grupos[i];
            }
        }
        return null;
    }

    function dondeEsta(id) {
        for (var i = 0; i < state.grupos.length; i++) {
            if (state.grupos[i].jugadores.indexOf(id) !== -1) {
                return state.grupos[i];
            }
        }
        return state.sinGrupo.indexOf(id) !== -1 ? 'banquillo' : null;
    }

    function ordenarGrupos() {
        // Orden estable por categoría (Array.prototype.sort es estable en navegadores actuales).
        state.grupos.sort(function (a, b) { return a.categoria - b.categoria; });
    }

    function quitarDeTodas(id) {
        state.grupos.forEach(function (g) {
            g.jugadores = g.jugadores.filter(function (j) { return j !== id; });
        });
        state.sinGrupo = state.sinGrupo.filter(function (j) { return j !== id; });
    }

    function colocar(id, destinoUid, indice) {
        quitarDeTodas(id);
        var lista = destinoUid === 'banquillo' ? state.sinGrupo : grupoPorUid(destinoUid).jugadores;
        if (indice === undefined || indice === null || indice > lista.length) {
            lista.push(id);
        } else {
            lista.splice(indice, 0, id);
        }
    }

    function nombreDestino(uid) {
        return uid === 'banquillo' ? 'Sin grupo' : grupoPorUid(uid).nombre;
    }

    /* ---------- historial y borrador ---------- */

    function instantanea() {
        return JSON.stringify({
            grupos: state.grupos,
            sinGrupo: state.sinGrupo,
            movimientos: state.movimientos,
            siguienteUid: state.siguienteUid
        });
    }

    function restaurar(snap) {
        var s = JSON.parse(snap);
        state.grupos = s.grupos;
        state.sinGrupo = s.sinGrupo;
        state.movimientos = s.movimientos;
        state.siguienteUid = s.siguienteUid;
    }

    /**
     * Aplica un cambio con posibilidad de deshacer.
     * opciones.anuncio: texto para lectores de pantalla.
     * opciones.foco: clave data-fk del elemento que debe recibir el foco.
     */
    function cambiar(mutacion, opciones) {
        opciones = opciones || {};
        historial.push(instantanea());
        if (historial.length > 100) {
            historial.shift();
        }
        rehacer = [];
        mutacion();
        guardarBorrador();
        renderTodo(opciones.foco);
        if (opciones.anuncio) {
            anunciar(opciones.anuncio);
        }
    }

    function deshacer() {
        if (!historial.length) {
            return;
        }
        rehacer.push(instantanea());
        restaurar(historial.pop());
        guardarBorrador();
        renderTodo();
        anunciar('Cambio deshecho.');
    }

    function rehacerCambio() {
        if (!rehacer.length) {
            return;
        }
        historial.push(instantanea());
        restaurar(rehacer.pop());
        guardarBorrador();
        renderTodo();
        anunciar('Cambio rehecho.');
    }

    function storage() {
        try {
            return window.localStorage;
        } catch (e) {
            return null;
        }
    }

    function guardarBorrador() {
        var s = storage();
        if (!s) {
            return;
        }
        try {
            s.setItem(DRAFT_KEY, JSON.stringify({ v: 1, t: Date.now(), nombre: state.nombre, estado: instantanea() }));
        } catch (e) { /* sin espacio o bloqueado: el borrador es solo una comodidad */ }
    }

    function leerBorrador() {
        var s = storage();
        if (!s) {
            return null;
        }
        try {
            var b = JSON.parse(s.getItem(DRAFT_KEY));
            return b && b.v === 1 && b.estado ? b : null;
        } catch (e) {
            return null;
        }
    }

    function borrarBorrador() {
        var s = storage();
        if (s) {
            try { s.removeItem(DRAFT_KEY); } catch (e) { /* nada */ }
        }
    }

    /** Descarta del borrador jugadores que ya no existen o repetidos. */
    function sanearEstado() {
        var vistos = {};
        function valido(id) {
            if (!JUGADORES[id] || vistos[id]) {
                return false;
            }
            vistos[id] = true;
            return true;
        }
        state.grupos.forEach(function (g) { g.jugadores = g.jugadores.filter(valido); });
        state.sinGrupo = state.sinGrupo.filter(valido);
    }

    function hace(ms) {
        var min = Math.round(ms / 60000);
        if (min < 1) { return 'hace un momento'; }
        if (min < 60) { return 'hace ' + plural(min, 'minuto', 'minutos'); }
        var horas = Math.round(min / 60);
        if (horas < 48) { return 'hace ' + plural(horas, 'hora', 'horas'); }
        return 'hace ' + plural(Math.round(horas / 24), 'día', 'días');
    }

    /* ---------- render: foco ---------- */

    function claveFoco() {
        var el = document.activeElement;
        return el && el.getAttribute ? el.getAttribute('data-fk') : null;
    }

    function enfocar(clave) {
        if (!clave) {
            return false;
        }
        var el = document.querySelector('[data-fk="' + clave.replace(/"/g, '') + '"]');
        if (el && el.disabled && el.getAttribute('data-fk-alt')) {
            el = document.querySelector('[data-fk="' + el.getAttribute('data-fk-alt') + '"]');
        }
        if (el && !el.disabled) {
            el.focus();
            return true;
        }
        return false;
    }

    function renderTodo(foco) {
        var previo = claveFoco();
        renderAnterior();
        renderTablero();
        renderBanquillo();
        renderRevision();
        renderBotonesHistorial();
        if (!enfocar(foco) && previo) {
            enfocar(previo);
        }
    }

    /* ---------- paso 1: clasificación anterior ---------- */

    function stepper(division, campo, lim, cats) {
        var m = state.movimientos[division.id] || { sube: 0, baja: 0 };
        var valor = m[campo];
        var puede = campo === 'sube' ? lim.puedeSubir : lim.puedeBajar;
        var destino = campo === 'sube' ? lim.destinoSube : lim.destinoBaja;
        var labelId = 'lb-st-' + division.id + '-' + campo;
        var verbo = campo === 'sube' ? 'Suben' : 'Bajan';

        if (!puede) {
            return h('p', { class: 'lb-stepper lb-stepper--off' },
                icono(campo === 'sube' ? 'vertical_align_top' : 'vertical_align_bottom'),
                campo === 'sube' ? 'Categoría más alta: no sube nadie' : 'Última categoría: no baja nadie');
        }

        var etiquetaDestino = etiquetaCategoria(destino);
        var total = division.clasificacion.length;
        var otro = campo === 'sube' ? m.baja : m.sube;

        function ajustar(delta) {
            return function () {
                cambiar(function () {
                    var nuevo = Object.assign({}, state.movimientos[division.id] || { sube: 0, baja: 0 });
                    nuevo[campo] = Math.max(0, Math.min(nuevo[campo] + delta, total - otro));
                    state.movimientos[division.id] = nuevo;
                    regenerar();
                }, {
                    anuncio: verbo + ' ' + (valor + delta) + ' en ' + division.nombre + '. Tablas de la nueva liga recalculadas; puedes deshacerlo.'
                });
            };
        }

        return h('div', { class: 'lb-stepper', role: 'group', 'aria-labelledby': labelId },
            h('span', { class: 'lb-stepper-label', id: labelId },
                icono(campo === 'sube' ? 'arrow_upward' : 'arrow_downward'),
                verbo + ' a ' + etiquetaDestino),
            h('button', {
                type: 'button', class: 'btn btn-default waves-effect lb-stepper-btn',
                'aria-label': verbo + ' uno menos', title: verbo + ' uno menos',
                'data-fk': 'st-' + division.id + '-' + campo + '-menos',
                'data-fk-alt': 'st-' + division.id + '-' + campo + '-mas',
                disabled: valor <= 0, onclick: ajustar(-1)
            }, icono('remove')),
            h('span', { class: 'lb-stepper-value' }, String(valor)),
            h('button', {
                type: 'button', class: 'btn btn-default waves-effect lb-stepper-btn',
                'aria-label': verbo + ' uno más', title: verbo + ' uno más',
                'data-fk': 'st-' + division.id + '-' + campo + '-mas',
                'data-fk-alt': 'st-' + division.id + '-' + campo + '-menos',
                disabled: valor + otro >= total, onclick: ajustar(1)
            }, icono('add'))
        );
    }

    function etiquetaCategoria(cat) {
        var g = LB.gruposDeCategoria(state.grupos, cat)[0];
        return g ? g.etiqueta : cat + 'ª';
    }

    var TEXTO_TIPO = { sube: 'Sube', baja: 'Baja', mantiene: 'Se mantiene' };
    var ICONO_TIPO = { sube: 'arrow_upward', baja: 'arrow_downward', mantiene: 'remove' };

    function renderAnterior() {
        var cont = byId('lb-prev');
        var cats = LB.categorias(state.grupos);
        var origen = origenActual();
        cont.textContent = '';

        if (!DIVISIONES.length) {
            cont.appendChild(h('div', { class: 'col-xs-12' },
                h('p', { class: 'alert alert-info' }, 'La liga de referencia no tiene grupos. Crea los grupos en el paso 2 y añade los jugadores.')));
            return;
        }

        DIVISIONES.forEach(function (d) {
            var lim = LB.limites(d, cats);
            var hId = 'lb-prev-h-' + d.id;
            var filas = d.clasificacion.map(function (f) {
                var o = origen[f.id];
                var donde = dondeEsta(f.id);
                var textoDonde = donde === 'banquillo' || !donde ? 'Sin grupo' : donde.nombre;
                return h('tr', { class: 'lb-row lb-row--' + o.tipo },
                    h('td', { class: 'lb-num' }, f.posicion + 'º'),
                    h('th', { scope: 'row', class: 'lb-name' }, f.nombre),
                    h('td', { class: 'lb-num' }, String(f.partidos)),
                    h('td', { class: 'lb-num' }, String(f.puntos)),
                    h('td', { class: 'lb-num hidden-xs' }, String(f.difSets)),
                    h('td', { class: 'lb-num hidden-xs' }, String(f.difJuegos)),
                    h('td', { class: 'lb-dest' },
                        h('span', { class: 'lb-chip lb-chip--' + o.tipo },
                            icono(ICONO_TIPO[o.tipo]), TEXTO_TIPO[o.tipo]),
                        h('span', { class: 'lb-dest-name' + (donde === 'banquillo' || !donde ? ' lb-dest-name--none' : '') }, textoDonde))
                );
            });

            var tabla = h('div', { class: 'table-responsive' },
                h('table', { class: 'table table-condensed lb-table' },
                    h('caption', { class: 'sr-only' }, 'Clasificación final de ' + d.nombre + ' y grupo en la nueva liga'),
                    h('thead', null, h('tr', null,
                        h('th', { scope: 'col', class: 'lb-num' }, h('abbr', { title: 'Posición' }, 'Pos.')),
                        h('th', { scope: 'col' }, 'Jugador'),
                        h('th', { scope: 'col', class: 'lb-num' }, h('abbr', { title: 'Partidos jugados' }, 'PJ')),
                        h('th', { scope: 'col', class: 'lb-num' }, h('abbr', { title: 'Puntos' }, 'Pts')),
                        h('th', { scope: 'col', class: 'lb-num hidden-xs' }, h('abbr', { title: 'Diferencia de sets' }, 'DS')),
                        h('th', { scope: 'col', class: 'lb-num hidden-xs' }, h('abbr', { title: 'Diferencia de juegos' }, 'DJ')),
                        h('th', { scope: 'col' }, 'Nueva liga')
                    )),
                    h('tbody', null, filas)
                ));

            cont.appendChild(h('div', { class: 'col-lg-6 col-xs-12' },
                h('section', { class: 'lb-prev-card', 'aria-labelledby': hId },
                    h('h3', { id: hId, class: 'lb-prev-title' }, d.nombre,
                        h('span', { class: 'lb-sub' }, plural(d.clasificacion.length, 'jugador', 'jugadores'))),
                    tabla,
                    h('div', { class: 'lb-steppers' },
                        stepper(d, 'sube', lim, cats),
                        stepper(d, 'baja', lim, cats))
                )));
        });
    }

    /** Recalcula los grupos con la estructura y los movimientos actuales. */
    function regenerar() {
        var propuesta = LB.proponer(DIVISIONES, state.grupos, state.movimientos);
        var enLigaAnterior = {};
        DIVISIONES.forEach(function (d) {
            d.clasificacion.forEach(function (f) { enLigaAnterior[f.id] = true; });
        });
        // Los añadidos a mano (que no jugaron la liga anterior) se conservan donde estaban.
        state.grupos.forEach(function (g, i) {
            g.jugadores.forEach(function (id) {
                if (!enLigaAnterior[id]) {
                    propuesta.grupos[i].jugadores.push(id);
                }
            });
        });
        state.grupos = propuesta.grupos;
        state.sinGrupo = propuesta.sinGrupo.concat(state.sinGrupo.filter(function (id) { return !enLigaAnterior[id]; }));
    }

    /* ---------- paso 2: tablero ---------- */

    function avisoTamano(n) {
        if (n === 0) {
            return 'Grupo vacío: no se puede crear así';
        }
        if (n < MIN_JUGADORES) {
            return 'Solo ' + plural(n, 'jugador', 'jugadores');
        }
        if (n > MAX_JUGADORES) {
            return plural(n, 'jugador', 'jugadores') + ': grupo muy grande';
        }
        return null;
    }

    function chipOrigen(id, origen) {
        var o = origen[id];
        var j = JUGADORES[id];
        var chips = [];
        if (o) {
            chips.push(h('span', { class: 'lb-chip lb-chip--' + o.tipo, title: o.nombre + ': ' + o.posicion + 'º de ' + o.total + ' (' + TEXTO_TIPO[o.tipo].toLowerCase() + ')' },
                icono(ICONO_TIPO[o.tipo]),
                h('span', { 'aria-hidden': 'true' }, o.corto + ' · ' + o.posicion + 'º'),
                srOnly('Viene de ' + o.nombre + ', ' + o.posicion + 'º de ' + o.total + ', ' + TEXTO_TIPO[o.tipo].toLowerCase() + '.')));
        } else {
            chips.push(h('span', { class: 'lb-chip lb-chip--new' }, icono('fiber_new'), 'No jugó la anterior'));
        }
        if (j && !j.activo) {
            chips.push(h('span', { class: 'lb-chip lb-chip--inactive', title: 'Se reactivará al crear la liga' }, icono('person_off'), 'Inactivo'));
        }
        return chips;
    }

    function itemJugador(id, uid, origen) {
        var nombre = nombreJugador(id);
        var enBanquillo = uid === 'banquillo';
        var o = origen[id];
        return h('li', { class: 'lb-player' + (o ? ' lb-player--' + o.tipo : ' lb-player--new'), 'data-id': id },
            h('span', { class: 'lb-handle', 'aria-hidden': 'true', title: 'Arrastrar' }, icono('drag_indicator')),
            h('span', { class: 'lb-player-main' },
                h('span', { class: 'lb-player-name' }, nombre),
                h('span', { class: 'lb-player-chips' }, chipOrigen(id, origen))),
            h('span', { class: 'lb-player-actions' },
                h('button', {
                    type: 'button', class: 'btn btn-default waves-effect lb-btn-move',
                    'data-fk': 'move-' + id, 'aria-label': (enBanquillo ? 'Asignar grupo a ' : 'Mover a ') + nombre,
                    title: enBanquillo ? 'Asignar a un grupo' : 'Mover a otro grupo',
                    onclick: function () { abrirMover(id); }
                }, icono(enBanquillo ? 'group_add' : 'swap_horiz'), h('span', { class: 'lb-btn-text' }, enBanquillo ? 'Asignar' : 'Mover')),
                enBanquillo ? null : h('button', {
                    type: 'button', class: 'btn btn-link waves-effect lb-btn-remove',
                    'data-fk': 'remove-' + id, 'aria-label': 'Quitar a ' + nombre + ' de ' + nombreDestino(uid),
                    title: 'Quitar del grupo',
                    onclick: function () { quitarJugador(id, uid); }
                }, icono('close'))
            ));
    }

    function listaJugadores(uid, ids, etiqueta, origen) {
        return h('ol', { class: 'lb-players' + (uid === 'banquillo' ? ' lb-players--bench' : ''), 'data-uid': uid, 'aria-label': etiqueta },
            ids.map(function (id) { return itemJugador(id, uid, origen); }));
    }

    function renderTablero() {
        var cont = byId('lb-board');
        var origen = origenActual();
        destruirSortables();
        cont.textContent = '';

        var cats = LB.categorias(state.grupos);
        if (!cats.length) {
            cont.appendChild(h('p', { class: 'alert alert-info' }, 'No hay grupos. Pulsa «Añadir categoría» para empezar.'));
        }

        cats.forEach(function (cat) {
            var grupos = LB.gruposDeCategoria(state.grupos, cat);
            var etiqueta = grupos[0].etiqueta;
            var total = grupos.reduce(function (n, g) { return n + g.jugadores.length; }, 0);
            var hId = 'lb-cat-h-' + cat;

            var tarjetas = grupos.map(function (g) {
                var gh = 'lb-g-h-' + g.uid;
                var aviso = avisoTamano(g.jugadores.length);
                return h('div', { class: 'col-md-6 col-xs-12' },
                    h('section', { class: 'lb-group' + (aviso ? ' lb-group--warn' : ''), 'aria-labelledby': gh },
                        h('div', { class: 'lb-group-head' },
                            h('h4', { id: gh, class: 'lb-group-title' }, g.nombre,
                                h('span', { class: 'badge lb-count' }, plural(g.jugadores.length, 'jugador', 'jugadores'))),
                            h('div', { class: 'lb-group-actions' },
                                h('button', {
                                    type: 'button', class: 'btn btn-primary waves-effect', 'data-fk': 'add-' + g.uid,
                                    'aria-label': 'Añadir jugador a ' + g.nombre,
                                    onclick: function () { abrirAnadir(g.uid); }
                                }, icono('person_add'), h('span', { class: 'lb-btn-text' }, 'Añadir')),
                                h('button', {
                                    type: 'button', class: 'btn btn-default waves-effect lb-btn-icon', 'data-fk': 'rename-' + g.uid,
                                    'aria-label': 'Renombrar ' + g.nombre, title: 'Renombrar',
                                    onclick: function () { abrirRenombrar(g.uid); }
                                }, icono('edit')),
                                h('button', {
                                    type: 'button', class: 'btn btn-default waves-effect lb-btn-icon', 'data-fk': 'delete-' + g.uid,
                                    'aria-label': 'Eliminar ' + g.nombre, title: 'Eliminar grupo',
                                    onclick: function () { eliminarGrupo(g.uid); }
                                }, icono('delete_outline'))
                            )),
                        aviso ? h('p', { class: 'lb-warn' }, icono('warning'), aviso) : null,
                        listaJugadores(g.uid, g.jugadores, 'Jugadores de ' + g.nombre, origen)
                    ));
            });

            cont.appendChild(h('section', { class: 'lb-cat', 'aria-labelledby': hId },
                h('div', { class: 'lb-cat-head' },
                    h('h3', { id: hId, class: 'lb-cat-title' }, 'Categoría ' + etiqueta,
                        h('span', { class: 'lb-sub' }, plural(total, 'jugador', 'jugadores') + ' · ' + plural(grupos.length, 'grupo', 'grupos'))),
                    h('div', { class: 'lb-cat-actions' },
                        grupos.length > 1 ? h('button', {
                            type: 'button', class: 'btn btn-default waves-effect', 'data-fk': 'deal-' + cat,
                            title: 'Reparte a todos los jugadores de la categoría alternando entre sus grupos',
                            onclick: function () { repartirCategoria(cat); }
                        }, icono('shuffle'), h('span', { class: 'lb-btn-text' }, 'Repartir alternando')) : null,
                        h('button', {
                            type: 'button', class: 'btn btn-default waves-effect', 'data-fk': 'addgroup-' + cat,
                            onclick: function () { anadirGrupo(cat); }
                        }, icono('add'), h('span', { class: 'lb-btn-text' }, 'Añadir grupo a ' + etiqueta))
                    )),
                h('div', { class: 'row lb-grid' }, tarjetas)));
        });

        crearSortables();

        var totalJugadores = state.grupos.reduce(function (n, g) { return n + g.jugadores.length; }, 0);
        byId('lb-counter').textContent = plural(totalJugadores, 'jugador', 'jugadores') + ' en ' +
            plural(state.grupos.length, 'grupo', 'grupos') + ' · ' + state.sinGrupo.length + ' sin grupo';
    }

    function renderBanquillo() {
        var cont = byId('lb-bench');
        var origen = origenActual();
        cont.textContent = '';
        cont.appendChild(h('h3', { id: 'lb-bench-title', class: 'lb-cat-title' }, 'Sin grupo',
            h('span', { class: 'badge lb-count' }, String(state.sinGrupo.length))));
        cont.appendChild(h('p', { class: 'lb-intro' },
            'Jugadores que no jugarán esta liga: los que has quitado y los de la liga anterior sin grupo de destino. ' +
            'Usa «Asignar» o arrástralos a un grupo para devolverlos.'));
        cont.appendChild(listaJugadores('banquillo', state.sinGrupo, 'Jugadores sin grupo', origen));
        crearSortable(cont.querySelector('.lb-players'));
    }

    function renderBotonesHistorial() {
        byId('lb-undo').disabled = !historial.length;
        byId('lb-redo').disabled = !rehacer.length;
    }

    /* ---------- arrastrar y soltar ---------- */

    function destruirSortables() {
        sortables.forEach(function (s) { s.destroy(); });
        sortables = [];
    }

    function crearSortables() {
        Array.prototype.forEach.call(byId('lb-board').querySelectorAll('.lb-players'), crearSortable);
    }

    function crearSortable(lista) {
        if (!window.Sortable || !lista) {
            return;
        }
        sortables.push(window.Sortable.create(lista, {
            group: 'lb-jugadores',
            handle: '.lb-handle',
            draggable: '.lb-player',
            animation: REDUCIR_MOVIMIENTO ? 0 : 150,
            ghostClass: 'lb-ghost',
            chosenClass: 'lb-chosen',
            dragClass: 'lb-dragging',
            fallbackTolerance: 3,
            scroll: true,
            bubbleScroll: true,
            onEnd: function (evt) {
                if (evt.from === evt.to && evt.oldDraggableIndex === evt.newDraggableIndex) {
                    return;
                }
                var id = Number(evt.item.getAttribute('data-id'));
                var destino = evt.to.getAttribute('data-uid');
                var mismo = evt.from === evt.to;
                cambiar(function () {
                    colocar(id, destino, evt.newDraggableIndex);
                }, {
                    anuncio: mismo ? nombreJugador(id) + ' reordenado.' : nombreJugador(id) + ' movido a ' + nombreDestino(destino) + '.',
                    foco: 'move-' + id
                });
            }
        }));
    }

    /* ---------- acciones ---------- */

    function quitarJugador(id, uid) {
        var desde = nombreDestino(uid);
        cambiar(function () {
            colocar(id, 'banquillo');
        }, {
            anuncio: nombreJugador(id) + ' quitado de ' + desde + '. Está en «Sin grupo»; puedes deshacerlo.',
            foco: siguienteFoco(id, uid)
        });
    }

    /** Tras quitar a un jugador, el foco pasa al siguiente de la lista (o al botón Añadir del grupo). */
    function siguienteFoco(id, uid) {
        var g = grupoPorUid(uid);
        if (!g) {
            return null;
        }
        var i = g.jugadores.indexOf(id);
        var siguiente = g.jugadores[i + 1] !== undefined ? g.jugadores[i + 1] : g.jugadores[i - 1];
        return siguiente !== undefined ? 'remove-' + siguiente : 'add-' + uid;
    }

    function abrirMover(id) {
        moveTarget = id;
        var actual = dondeEsta(id);
        var actualUid = actual === 'banquillo' ? 'banquillo' : (actual ? actual.uid : null);
        var nombre = nombreJugador(id);
        var cuerpo = byId('lb-move-body');
        byId('lb-move-title').textContent = 'Mover a ' + nombre;
        cuerpo.textContent = '';

        function opcion(uid, texto, n) {
            var esActual = uid === actualUid;
            return h('li', null, h('button', {
                type: 'button', class: 'btn btn-block waves-effect lb-move-option' + (esActual ? ' lb-move-option--current' : ''),
                disabled: esActual, 'aria-current': esActual ? 'true' : null,
                onclick: function () {
                    $('#lb-move-modal').modal('hide');
                    cambiar(function () { colocar(id, uid); }, {
                        anuncio: nombre + ' movido a ' + texto + '.',
                        foco: 'move-' + id
                    });
                }
            }, h('span', null, texto), h('span', { class: 'lb-move-meta' }, esActual ? 'Está aquí' : n)));
        }

        LB.categorias(state.grupos).forEach(function (cat) {
            var grupos = LB.gruposDeCategoria(state.grupos, cat);
            cuerpo.appendChild(h('h5', { class: 'lb-move-cat' }, 'Categoría ' + grupos[0].etiqueta));
            cuerpo.appendChild(h('ul', { class: 'lb-move-list' }, grupos.map(function (g) {
                return opcion(g.uid, g.nombre, plural(g.jugadores.length, 'jugador', 'jugadores'));
            })));
        });
        cuerpo.appendChild(h('h5', { class: 'lb-move-cat' }, 'Fuera de la liga'));
        cuerpo.appendChild(h('ul', { class: 'lb-move-list' }, opcion('banquillo', 'Sin grupo (no juega)', plural(state.sinGrupo.length, 'jugador', 'jugadores'))));

        $('#lb-move-modal').modal('show');
    }

    function abrirAnadir(uid) {
        addTarget = uid;
        byId('lb-add-title').textContent = 'Añadir jugador a ' + grupoPorUid(uid).nombre;
        byId('lb-search').value = '';
        renderBusqueda();
        $('#lb-add-modal').modal('show');
    }

    function renderBusqueda(foco) {
        var g = grupoPorUid(addTarget);
        if (!g) {
            return;
        }
        var q = LB.normalizar(byId('lb-search').value);
        var conInactivos = byId('lb-search-inactive').checked;
        var lista = byId('lb-search-results');
        var resultados = DATA.jugadores.filter(function (j) {
            return (conInactivos || j.activo) && (!q || LB.normalizar(j.nombre).indexOf(q) !== -1);
        }).sort(function (a, b) {
            return (b.activo - a.activo) || a.nombre.localeCompare(b.nombre, 'es');
        });

        lista.textContent = '';
        resultados.forEach(function (j) {
            var donde = dondeEsta(j.id);
            var aqui = donde && donde !== 'banquillo' && donde.uid === addTarget;
            var enOtro = donde && donde !== 'banquillo' && !aqui;
            var textoBoton = aqui ? 'Ya está' : (enOtro ? 'Mover aquí' : 'Añadir');
            lista.appendChild(h('li', { class: 'lb-result' + (aqui ? ' lb-result--here' : '') },
                h('span', { class: 'lb-result-main' },
                    h('span', { class: 'lb-player-name' }, j.nombre),
                    h('span', { class: 'lb-player-chips' },
                        j.activo ? null : h('span', { class: 'lb-chip lb-chip--inactive' }, icono('person_off'), 'Inactivo'),
                        enOtro ? h('span', { class: 'lb-chip lb-chip--mantiene' }, icono('group'), 'En ' + donde.nombre) : null,
                        donde === 'banquillo' ? h('span', { class: 'lb-chip lb-chip--mantiene' }, icono('event_busy'), 'Sin grupo') : null)),
                h('button', {
                    type: 'button', class: 'btn waves-effect ' + (aqui ? 'btn-default' : 'btn-primary'),
                    'data-fk': 'result-' + j.id,
                    'aria-disabled': aqui ? 'true' : null,
                    'aria-label': aqui ? j.nombre + ' ya está en ' + g.nombre : textoBoton + ' a ' + j.nombre + (enOtro ? ' desde ' + donde.nombre : ''),
                    onclick: function () {
                        if (aqui) {
                            return;
                        }
                        cambiar(function () { colocar(j.id, addTarget); }, {
                            anuncio: j.nombre + ' añadido a ' + g.nombre + '.' + (enOtro ? ' Ya no está en ' + donde.nombre + '.' : '')
                        });
                        renderBusqueda('result-' + j.id);
                    }
                }, aqui ? icono('check') : icono(enOtro ? 'swap_horiz' : 'add'), h('span', null, textoBoton))
            ));
        });

        var estado = resultados.length ? plural(resultados.length, 'jugador encontrado', 'jugadores encontrados') : 'No hay jugadores con ese nombre.';
        byId('lb-search-status').textContent = estado;
        if (foco) {
            enfocar(foco);
        }
    }

    function abrirRenombrar(uid) {
        renameTarget = uid;
        var g = grupoPorUid(uid);
        byId('lb-rename-title').textContent = 'Renombrar ' + g.nombre;
        byId('lb-rename-input').value = g.auto ? '' : g.nombre;
        byId('lb-rename-input').placeholder = g.nombre;
        $('#lb-rename-modal').modal('show');
    }

    function guardarNombreGrupo(evt) {
        evt.preventDefault();
        var g = grupoPorUid(renameTarget);
        var nuevo = byId('lb-rename-input').value.trim();
        $('#lb-rename-modal').modal('hide');
        cambiar(function () {
            g.auto = nuevo === '';
            if (!g.auto) {
                g.nombre = nuevo;
            }
            LB.renombrarCategoria(state.grupos, g.categoria);
        }, { anuncio: 'Grupo renombrado a ' + (nuevo || 'nombre automático') + '.', foco: 'rename-' + g.uid });
    }

    function eliminarGrupo(uid) {
        var g = grupoPorUid(uid);
        var n = g.jugadores.length;
        var hacer = function () {
            var cat = g.categoria;
            cambiar(function () {
                state.sinGrupo = state.sinGrupo.concat(g.jugadores);
                state.grupos = state.grupos.filter(function (x) { return x.uid !== uid; });
                LB.renombrarCategoria(state.grupos, cat);
            }, {
                anuncio: 'Grupo ' + g.nombre + ' eliminado.' + (n ? ' Sus jugadores están en «Sin grupo».' : '') + ' Puedes deshacerlo.',
                foco: LB.gruposDeCategoria(state.grupos, cat).length > 1 ? 'addgroup-' + cat : null
            });
            if (!document.activeElement || document.activeElement === document.body) {
                byId('lb-add-category').focus();
            }
        };
        if (!n) {
            hacer();
            return;
        }
        swal({
            title: '¿Eliminar ' + g.nombre + '?',
            text: 'Sus ' + n + ' jugadores pasarán a «Sin grupo». Podrás deshacerlo.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }, function (ok) {
            if (ok) {
                // sweetalert devuelve el foco al cerrarse: esperamos a que termine.
                setTimeout(hacer, 50);
            }
        });
    }

    function anadirGrupo(cat) {
        var uid = 'g' + state.siguienteUid;
        var etiqueta = etiquetaCategoria(cat);
        cambiar(function () {
            state.siguienteUid++;
            state.grupos.push({ uid: uid, categoria: cat, etiqueta: etiqueta, nombre: etiqueta, auto: true, jugadores: [] });
            ordenarGrupos();
            LB.renombrarCategoria(state.grupos, cat);
        }, { foco: 'add-' + uid });
        anunciar('Nuevo grupo ' + grupoPorUid(uid).nombre + ' creado. Puedes usar «Repartir alternando» para repartir la categoría.');
    }

    function anadirCategoria() {
        var cats = LB.categorias(state.grupos);
        var cat = cats.length ? cats[cats.length - 1] + 1 : 1;
        var ultima = cats.length ? etiquetaCategoria(cats[cats.length - 1]) : null;
        var numero = ultima && /^\d+/.test(ultima) ? parseInt(ultima, 10) + 1 : cat;
        var etiqueta = numero + 'ª';
        var uid = 'g' + state.siguienteUid;
        cambiar(function () {
            state.siguienteUid++;
            state.grupos.push({ uid: uid, categoria: cat, etiqueta: etiqueta, nombre: etiqueta, auto: true, jugadores: [] });
            ordenarGrupos();
        }, { anuncio: 'Categoría ' + etiqueta + ' creada con un grupo vacío.', foco: 'add-' + uid });
    }

    function repartirCategoria(cat) {
        var etiqueta = etiquetaCategoria(cat);
        cambiar(function () {
            LB.repartir(state.grupos, cat, origenActual());
        }, { anuncio: 'Jugadores de la categoría ' + etiqueta + ' repartidos alternando entre sus grupos.', foco: 'deal-' + cat });
    }

    function volverAPropuesta() {
        swal({
            title: '¿Volver a la propuesta?',
            text: 'Se recalculan las tablas con la clasificación y los movimientos del paso 1. Se mantiene la estructura de grupos actual. Podrás deshacerlo.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, recalcular',
            cancelButtonText: 'Cancelar'
        }, function (ok) {
            if (ok) {
                setTimeout(function () {
                    cambiar(regenerar, { anuncio: 'Tablas recalculadas.', foco: null });
                    byId('lb-reset').focus();
                }, 50);
            }
        });
    }

    /* ---------- paso 3: revisión ---------- */

    function problemas() {
        var errores = [];
        var avisos = [];
        var nombre = state.nombre.trim();
        var existentes = DATA.ligas.map(function (l) { return LB.normalizar(l.nombre); });

        if (!nombre) {
            errores.push({ texto: 'Falta el nombre de la liga.', foco: 'lb-nombre' });
        } else if (existentes.indexOf(LB.normalizar(nombre)) !== -1) {
            errores.push({ texto: 'Ya existe una liga llamada «' + nombre + '».', foco: 'lb-nombre' });
        }
        if (!state.grupos.length) {
            errores.push({ texto: 'La liga no tiene grupos.' });
        }

        var vistos = {};
        state.grupos.forEach(function (g) {
            var clave = LB.normalizar(g.nombre);
            if (vistos[clave]) {
                errores.push({ texto: 'Hay dos grupos llamados «' + g.nombre + '».', tab: 2, fk: 'rename-' + g.uid });
            }
            vistos[clave] = true;
            if (!g.jugadores.length) {
                errores.push({ texto: g.nombre + ' está vacío: añade jugadores o elimínalo.', tab: 2, fk: 'add-' + g.uid });
            } else if (g.jugadores.length < MIN_JUGADORES || g.jugadores.length > MAX_JUGADORES) {
                avisos.push({ texto: g.nombre + ' tiene ' + plural(g.jugadores.length, 'jugador', 'jugadores') + '.', tab: 2, fk: 'add-' + g.uid });
            }
        });

        var anteriores = {};
        DIVISIONES.forEach(function (d) {
            d.clasificacion.forEach(function (f) { anteriores[f.id] = true; });
        });
        var fuera = state.sinGrupo.filter(function (id) { return anteriores[id]; });
        if (fuera.length) {
            avisos.push({
                texto: plural(fuera.length, 'jugador', 'jugadores') + ' de la liga anterior no ' + (fuera.length === 1 ? 'jugará' : 'jugarán') + ': ' +
                    fuera.map(nombreJugador).join(', ') + '.',
                tab: 2, fk: null, banquillo: true
            });
        }

        var inactivos = [];
        state.grupos.forEach(function (g) {
            g.jugadores.forEach(function (id) {
                if (JUGADORES[id] && !JUGADORES[id].activo) {
                    inactivos.push(nombreJugador(id));
                }
            });
        });

        return { errores: errores, avisos: avisos, inactivos: inactivos };
    }

    function enlaceProblema(p) {
        if (p.foco) {
            return h('button', { type: 'button', class: 'btn btn-link lb-fix', onclick: function () { byId(p.foco).focus(); } }, 'Corregir');
        }
        if (p.tab) {
            return h('button', {
                type: 'button', class: 'btn btn-link lb-fix', onclick: function () {
                    activarPestana(p.tab, false);
                    if (p.banquillo) {
                        byId('lb-bench').scrollIntoView();
                        var btn = byId('lb-bench').querySelector('button');
                        (btn || byId('lb-bench')).focus();
                    } else if (!enfocar(p.fk)) {
                        byId('lb-panel-2').focus();
                    }
                }
            }, 'Ir al grupo');
        }
        return null;
    }

    function renderRevision() {
        var cont = byId('lb-review');
        var p = problemas();
        var totalJugadores = state.grupos.reduce(function (n, g) { return n + g.jugadores.length; }, 0);
        cont.textContent = '';

        cont.appendChild(h('div', { class: 'lb-summary' },
            h('div', { class: 'lb-summary-item' }, h('span', { class: 'lb-summary-value' }, state.nombre.trim() || '(sin nombre)'), h('span', { class: 'lb-summary-label' }, 'Nombre')),
            h('div', { class: 'lb-summary-item' }, h('span', { class: 'lb-summary-value' }, String(LB.categorias(state.grupos).length)), h('span', { class: 'lb-summary-label' }, 'Categorías')),
            h('div', { class: 'lb-summary-item' }, h('span', { class: 'lb-summary-value' }, String(state.grupos.length)), h('span', { class: 'lb-summary-label' }, 'Grupos')),
            h('div', { class: 'lb-summary-item' }, h('span', { class: 'lb-summary-value' }, String(totalJugadores)), h('span', { class: 'lb-summary-label' }, 'Jugadores'))
        ));

        if (p.errores.length) {
            cont.appendChild(h('div', { class: 'alert lb-alert lb-alert--error' },
                h('h3', { class: 'lb-alert-title' }, icono('error_outline'), 'Hay que corregir antes de crear la liga'),
                h('ul', null, p.errores.map(function (e) { return h('li', null, e.texto, ' ', enlaceProblema(e)); }))));
        }
        if (p.avisos.length) {
            cont.appendChild(h('div', { class: 'alert lb-alert lb-alert--warn' },
                h('h3', { class: 'lb-alert-title' }, icono('warning'), 'Revisa (no impide crear la liga)'),
                h('ul', null, p.avisos.map(function (a) { return h('li', null, a.texto, ' ', enlaceProblema(a)); }))));
        }
        if (p.inactivos.length) {
            cont.appendChild(h('div', { class: 'alert lb-alert lb-alert--info' },
                h('h3', { class: 'lb-alert-title' }, icono('person_add_alt'), 'Se reactivarán al crear la liga'),
                h('p', null, p.inactivos.join(', ') + '.')));
        }
        cont.appendChild(h('div', { class: 'alert lb-alert lb-alert--info' },
            h('p', null, icono('info_outline'), ' Al crearla, será la ', h('strong', null, 'liga actual'), ' de la web y los socios verán sus nuevos grupos.')));

        cont.appendChild(h('h3', { class: 'lb-cat-title' }, 'Grupos'));
        cont.appendChild(h('div', { class: 'row lb-grid' }, state.grupos.map(function (g) {
            return h('div', { class: 'col-md-4 col-sm-6 col-xs-12' },
                h('section', { class: 'lb-review-group', 'aria-label': g.nombre },
                    h('h4', { class: 'lb-group-title' }, g.nombre, h('span', { class: 'badge lb-count' }, String(g.jugadores.length))),
                    g.jugadores.length ? h('ol', { class: 'lb-review-list' }, g.jugadores.map(function (id) {
                        return h('li', null, nombreJugador(id));
                    })) : h('p', { class: 'lb-warn' }, icono('warning'), 'Vacío')));
        })));

        var btn = byId('lb-save');
        btn.setAttribute('aria-disabled', p.errores.length || guardando ? 'true' : 'false');
        btn.classList.toggle('lb-disabled', !!p.errores.length);
    }

    /* ---------- guardar ---------- */

    function crearLiga() {
        if (guardando) {
            return;
        }
        var p = problemas();
        if (p.errores.length) {
            var alerta = byId('lb-review').querySelector('.lb-alert--error');
            if (alerta) {
                alerta.setAttribute('tabindex', '-1');
                alerta.focus();
            }
            anunciar('No se puede crear todavía: ' + plural(p.errores.length, 'problema', 'problemas') + ' por corregir.');
            return;
        }
        var totalJugadores = state.grupos.reduce(function (n, g) { return n + g.jugadores.length; }, 0);
        swal({
            title: '¿Crear «' + state.nombre.trim() + '»?',
            text: plural(state.grupos.length, 'grupo', 'grupos') + ' y ' + plural(totalJugadores, 'jugador', 'jugadores') +
                '. Pasará a ser la liga actual en la web.',
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#4CAF50',
            confirmButtonText: 'Sí, crear liga',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: true
        }, function (ok) {
            if (ok) {
                setTimeout(enviar, 50);
            }
        });
    }

    function enviar() {
        var btn = byId('lb-save');
        var errores = byId('lb-save-errors');
        guardando = true;
        btn.setAttribute('aria-disabled', 'true');
        btn.lastChild.textContent = ' Creando…';
        errores.hidden = true;
        anunciar('Creando la liga…');

        var cuerpo = {
            nombre: state.nombre.trim(),
            grupos: state.grupos.map(function (g) {
                return { nombre: g.nombre, categoria: g.categoria, jugadores: g.jugadores };
            })
        };

        fetch(CONFIG.saveUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CONFIG.csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(cuerpo)
        }).then(function (r) {
            return r.json().catch(function () {
                return { ok: false, errors: ['Respuesta inesperada del servidor (' + r.status + '). No se ha creado nada.'] };
            });
        }).then(function (res) {
            if (res.ok) {
                borrarBorrador();
                window.location.href = res.redirect;
                return;
            }
            mostrarErrores(res.errors || ['No se ha podido crear la liga.']);
        }).catch(function () {
            mostrarErrores(['No hay conexión con el servidor. Tu borrador sigue guardado; inténtalo de nuevo.']);
        });
    }

    function mostrarErrores(lista) {
        var cont = byId('lb-save-errors');
        guardando = false;
        byId('lb-save').lastChild.textContent = ' Crear liga';
        renderRevision();
        cont.textContent = '';
        cont.appendChild(h('p', null, h('strong', null, 'No se ha creado la liga:')));
        cont.appendChild(h('ul', null, lista.map(function (e) { return h('li', null, e); })));
        cont.hidden = false;
        cont.setAttribute('tabindex', '-1');
        cont.focus();
    }

    /* ---------- pestañas ---------- */

    function activarPestana(n, enfocarPestana) {
        [1, 2, 3].forEach(function (i) {
            var tab = byId('lb-tab-' + i);
            var activa = i === n;
            tab.setAttribute('aria-selected', activa ? 'true' : 'false');
            tab.setAttribute('tabindex', activa ? '0' : '-1');
            tab.parentNode.classList.toggle('active', activa);
            byId('lb-panel-' + i).hidden = !activa;
        });
        if (n === 3) {
            renderRevision();
        }
        if (enfocarPestana) {
            byId('lb-tab-' + n).focus();
        }
    }

    function initPestanas() {
        [1, 2, 3].forEach(function (i) {
            var tab = byId('lb-tab-' + i);
            tab.addEventListener('click', function (e) {
                e.preventDefault();
                activarPestana(i, false);
            });
            tab.addEventListener('keydown', function (e) {
                var destino = null;
                if (e.key === 'ArrowRight') { destino = i % 3 + 1; }
                if (e.key === 'ArrowLeft') { destino = (i + 1) % 3 + 1; }
                if (e.key === 'Home') { destino = 1; }
                if (e.key === 'End') { destino = 3; }
                if (destino) {
                    e.preventDefault();
                    activarPestana(destino, true);
                }
            });
        });
        Array.prototype.forEach.call(document.querySelectorAll('[data-goto-tab]'), function (btn) {
            btn.addEventListener('click', function () {
                var n = Number(btn.getAttribute('data-goto-tab'));
                activarPestana(n, false);
                byId('lb-tab-' + n).scrollIntoView({ block: 'start' });
                byId('lb-panel-' + n).focus({ preventScroll: true });
            });
        });
    }

    /* ---------- nombre de la liga ---------- */

    function validarNombre() {
        var aviso = byId('lb-nombre-aviso');
        var nombre = state.nombre.trim();
        var existe = DATA.ligas.some(function (l) { return LB.normalizar(l.nombre) === LB.normalizar(nombre); });
        aviso.textContent = !nombre ? 'Obligatorio.' : (existe ? 'Ya existe una liga con este nombre.' : '');
        aviso.classList.toggle('lb-field-hint--error', !nombre || existe);
        byId('lb-nombre').setAttribute('aria-invalid', !nombre || existe ? 'true' : 'false');
    }

    /* ---------- arranque ---------- */

    function init() {
        state = estadoInicial();
        var propuestaInicial = instantanea();

        var borrador = leerBorrador();
        if (borrador && (borrador.estado !== propuestaInicial || borrador.nombre !== state.nombre)) {
            byId('lb-draft-text').textContent = 'Tienes un borrador sin terminar para esta liga, guardado en este navegador ' + hace(Date.now() - borrador.t) + '.';
            byId('lb-draft-banner').hidden = false;
            byId('lb-draft-restore').addEventListener('click', function () {
                historial.push(instantanea());
                restaurar(borrador.estado);
                state.nombre = borrador.nombre || state.nombre;
                sanearEstado();
                byId('lb-nombre').value = state.nombre;
                validarNombre();
                byId('lb-draft-banner').hidden = true;
                renderTodo();
                anunciar('Borrador recuperado.');
                byId('lb-tab-2').focus();
            });
            byId('lb-draft-discard').addEventListener('click', function () {
                borrarBorrador();
                byId('lb-draft-banner').hidden = true;
                anunciar('Borrador descartado.');
                byId('lb-nombre').focus();
            });
        }

        var nombre = byId('lb-nombre');
        nombre.value = state.nombre;
        if (state.nombre) {
            nombre.parentNode.classList.add('focused');
        }
        nombre.addEventListener('input', function () {
            state.nombre = nombre.value;
            validarNombre();
            guardarBorrador();
            if (!byId('lb-panel-3').hidden) {
                renderRevision();
            }
        });
        validarNombre();

        byId('lb-undo').addEventListener('click', deshacer);
        byId('lb-redo').addEventListener('click', rehacerCambio);
        byId('lb-reset').addEventListener('click', volverAPropuesta);
        byId('lb-add-category').addEventListener('click', anadirCategoria);
        byId('lb-save').addEventListener('click', crearLiga);
        byId('lb-rename-form').addEventListener('submit', guardarNombreGrupo);
        byId('lb-search').addEventListener('input', function () { renderBusqueda(); });
        byId('lb-search-inactive').addEventListener('change', function () { renderBusqueda(); });

        document.addEventListener('keydown', function (e) {
            var t = e.target;
            var escribiendo = t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.tagName === 'SELECT' || t.isContentEditable);
            if (escribiendo || !(e.ctrlKey || e.metaKey) || $('.modal.in').length) {
                return;
            }
            var k = e.key.toLowerCase();
            if (k === 'z' && !e.shiftKey) {
                e.preventDefault();
                deshacer();
            } else if (k === 'y' || (k === 'z' && e.shiftKey)) {
                e.preventDefault();
                rehacerCambio();
            }
        });

        // Foco al abrir los diálogos y vuelta al elemento de origen al cerrarlos.
        var origenFoco = null;
        $('.modal').on('show.bs.modal', function () {
            origenFoco = claveFoco();
        });
        $('#lb-add-modal').on('shown.bs.modal', function () { byId('lb-search').focus(); });
        $('#lb-rename-modal').on('shown.bs.modal', function () { byId('lb-rename-input').focus(); });
        $('#lb-move-modal').on('shown.bs.modal', function () {
            var primero = byId('lb-move-body').querySelector('button:not([disabled])');
            if (primero) {
                primero.focus();
            }
        });
        $('.modal').on('hidden.bs.modal', function () {
            var activo = document.activeElement;
            if ((!activo || activo === document.body) && origenFoco) {
                enfocar(origenFoco);
            }
        });

        initPestanas();
        renderTodo();
        activarPestana(1, false);
    }

    init();
}());
