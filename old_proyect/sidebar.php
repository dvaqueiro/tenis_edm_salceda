<div class="sidebar_item">
            <h2>Nueva Web</h2>
            <p>Bienvenidos a la página web del Club de Tenis EDM Salceda de Caselas.</p>
            
            <div style="border-radius:.4em; background-color:#1F400F; color:white; padding:10px; margin-right:10px;">
            <?php
			if(isset($_SESSION['usutenis']) && $_SESSION['usutenis']!=''){
			?>
            Conectado como<br /><br /><strong><?php echo $_SESSION['usutenis']; ?></strong><br /><br />
            <a href="index.php?datos=1">modificar sus datos</a><br /><br />
            <a href="index.php?salir=1">salir</a>
            <?php }else{ ?>
            <div style="border-bottom:dashed 1px; margin-bottom:1em;">Acceder</div>
            	<form name="logueo" method="post">
                	DNI<br>
                    <input type="text" name="dnilog" style="width:95%; border-radius:.4em;"><br><br>
                    Contraseña<br>
                    <input type="password" style="width:95%; border-radius:.4em;"  name="contrasenalog"><br>
                    <input type="submit" style="cursor:pointer; width:60%; margin-top:.5em;" name="entrar" value="entrar">
                </form>
            <?php } ?>
            </div>
       <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/gl_ES/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<br />
<div class="fb-like-box" data-href="https://www.facebook.com/pages/Club-tenis-EDM-Salceda/546914465384980" data-width="210" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="true" data-show-border="true" style="background-color:#eee;"></div>
            
</div><!--close sidebar_item--> 