<?php
/*
Plugin Name: POP Under
Plugin URI: mailto:aloiv.f@libero.it
Description: It is a simply and powerfull pop-under manager plugin to edit, manage and take control of your pop-under ads
Author: Francesco Viola
Version: 1.0
Author URI: mailto:aloiv.f@libero.it
*/

// CONFIGURAZIONE DEL MENU
function POPUnder_config_page(){
  add_menu_page('POPUnder', 'POPUnder', 7, 'popunder_menu', 'POPUnder_config', get_option('home') . '/wp-includes/images/smilies/icon_eek.gif');
}

// FUNZIONI PAGINE
function POPUnder_config(){
	?>
	
    <style>
		#POPUnder_txt_script 	{ width:100%; height:150px; }
	</style>
    
    <h2>Thanks for using POPUnder</h2>
    <h3>Use the options below to set the plugin properly</h3>
        
    <form id="POPUnder_form" method="post" >
    	
        
        
        
        <table class="widefat" style=" width:95%;">
        	<thead><tr><th scope="col" colspan="2">Options</th></tr></thead>
			<tbody>
				<tr>
                	<td scope="col" style="width: 35%;">
                    	Insert ad code (only 336x280 right now)
					</td>
					<td scope="col" style="width: 65%;">
        				<textarea id="POPUnder_txt_script" name="POPUnder_txt_script"><?php echo stripslashes(get_option("POPUnder_script")); ?></textarea> <br/>            	
					</td>
                </tr>
                <tr>
                	<td scope="col" style="width: 35%;">
                    	Set the time interval between ad impressions (per single user)
					</td>
					<td scope="col" style="width: 65%;">
        				<select name="POPUnder_txt_intervallo_ora" id="POPUnder_txt_intervallo_ora" >
                        			<?php 
										$ora = get_option("POPUnder_intervallo_ora");
										for($i=0;$i<24;$i++) {
											echo "<option value=".$i;
											if($ora==$i) echo " selected='selected' ";
											echo ">".$i."</option>";
										}
									?>
                        </select> hour
                        <select name="POPUnder_txt_intervallo_minuto" id="POPUnder_txt_intervallo_minuto" >
                        			<?php 
										$minuti = get_option("POPUnder_intervallo_minuto");
										for($i=0;$i<60;$i++) {
											echo "<option value=".$i;
											if($minuti==$i) echo " selected='selected' ";
											echo ">".$i."</option>";
										}
									?>
                        </select> minutes
					</td>
                </tr>
                <tr>
                	<td scope="col" style="width: 35%;">
                    	Exit text
					</td>
					<td scope="col" style="width: 65%;">
        				<?php $exit = get_option("POPUnder_exit_text"); ?>
						 <Input type = 'text' Name ='POPUnder_txt_exit_text' value="<?php echo $exit; ?>"  >
                    </td>
                </tr>
                <tr>
                	<td scope="col" style="width: 35%;">
                    	Active in homepage
					</td>
					<td scope="col" style="width: 65%;">
        				<?php $attivo = get_option("POPUnder_attivo_home"); ?>
						                     
                         <Input type = 'Radio' Name ='POPUnder_rad_attivohome' value= '1' <?php if($attivo == 1) echo "checked='checked'"; ?>>Yes
                         <Input type = 'Radio' Name ='POPUnder_rad_attivohome' value= '0' <?php if($attivo == 0) echo "checked='checked'"; ?>>No
					</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                    	<p class="submit"><input type="submit" name="salva_script" id="salva_script" value="save options >"/></p>
                    </td>
           		</tr>
			</tbody>
		</table>
    </form>
        
    <?php
}



//OPERAZIONI
function update_POPUnder_settings(){

	if(isset($_POST["POPUnder_txt_script"]))
		update_option("POPUnder_script", $_POST["POPUnder_txt_script"] );
	
	if(isset($_POST["POPUnder_txt_intervallo_ora"]))
		update_option("POPUnder_intervallo_ora", $_POST["POPUnder_txt_intervallo_ora"] );
	
	if(isset($_POST["POPUnder_txt_intervallo_minuto"]))
		update_option("POPUnder_intervallo_minuto", $_POST["POPUnder_txt_intervallo_minuto"] );
		
	if(isset($_POST["POPUnder_rad_attivohome"]))
		update_option("POPUnder_attivo_home", $_POST["POPUnder_rad_attivohome"] );
		
	if(isset($_POST["POPUnder_txt_exit_text"]))
		update_option("POPUnder_exit_text", $_POST["POPUnder_txt_exit_text"] );
		
	
	
}

//VISUALIZZA LA PUBBLICITA' SOTTOFORMA DI POP UNDER
function visualizza_pubblicita(){
	
	$attivo_in_home = get_option("POPUnder_attivo_home");
	if($attivo_in_home==0) if(is_home()) return;
	
	global $wpdb;
	$wpdb->query("DELETE FROM POPUnder WHERE data  < ADDTIME( NOW( ) , '-24:00:00' ) ");
	
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$intervallo_ore = get_option("POPUnder_intervallo_ora"); if($intervallo_ore < 10) $intervallo_ore = "0".$intervallo_ore;
	$intervallo_minuti = get_option("POPUnder_intervallo_minuto"); if($intervallo_minuti < 10) $intervallo_minuti = "0".$intervallo_minuti;
	
	$esiste = $wpdb->get_var("SELECT ip FROM POPUnder WHERE ip='".$ip."' AND data  > ADDTIME( NOW( ) , '-".$intervallo_ore.":".$intervallo_minuti.":00' ) ",0,0);
		
	if($esiste==""){
		$wpdb->query(" INSERT INTO POPUnder(ip,data) VALUES('".$ip."',NOW()) ");
	
		echo "
				
			<script type='text/javascript'>
				function POPUnder_chiudi(){
					document.getElementById('POPUnder_sfondo').style.visibility = 'hidden';
					document.getElementById('POPUnder_banner').style.visibility = 'hidden';
				}
			</script>
			
			<style>
				#POPUnder_sfondo {width:100%; height:100%; background-color:#FFF; filter: alpha(opacity=80); opacity:0.8; text-align:center; float:left; z-index:100; position:absolute;}
				#POPUnder_banner {width:376px; height:335px; background-image:url('".get_option('home')."/wp-content/plugins/POPUnder/images/background_popunder.png'); position:absolute; left:35%; top:20%;  z-index:200;}
				#POPUnder_banner_script {margin-left:20px; margin-top:20px;}
				#POPUnder_banner_chiudi {margin-top:0px; margin-left:328px; color:#999; font-size:11px;}
				#POPUnder_banner_chiudi span {float:right; margin-right:19px;}
			</style>
			
			<div id='POPUnder_sfondo' >
			</div>
			
			<div id='POPUnder_banner' >
					<div id='POPUnder_banner_script' > ";
					
		echo stripslashes(get_option("POPUnder_script"));
					
		echo "
					</div>
					<div id='POPUnder_banner_chiudi'  >
						<span ><a href='#' onClick='POPUnder_chiudi();'>"; echo get_option("POPUnder_exit_text"); echo "</a></span>
					</div>
			</div>
			
		";
	}
}


//INSTALLAZIONE
add_action('admin_menu', 'POPUnder_config_page');
add_action('init', 'update_POPUnder_settings', 	9999);
add_action('wp_head', 'visualizza_pubblicita');
function POPUnder_install(){

	global $wpdb;
	$query_table = " CREATE TABLE  POPUnder (
  							id int(10) unsigned NOT NULL auto_increment,
  							ip varchar(50) NOT NULL,
  							data datetime NOT NULL,
  							PRIMARY KEY  (id)
					   ) ENGINE=InnoDB DEFAULT CHARSET=latin1; ";
	
	$wpdb->query($query_table);
	
	
	$script = "
		
		<!--  ADVERTISEMENT TAG 336 x 280, DO NOT MODIFY THIS CODE -->
		<script src='http://performance-by.simply.com/simply.js?code=6555;1;0&v=2'></script>
		<script language='JavaScript'>
		<!--
		document.write(\"<iframe marginheight='0px' marginwidth='0px' frameborder='0' scrolling='no' width='336' height='280' src='http://optimized-by.simply.com/play.html?code=23333;6421;5371;0&from=\"+escape(document.referrer)+\"'></iframe>\");
		// -->
		</script>
	";
	
	add_option("POPUnder_script", $script, 'Script che verrà visualizzato all\'intero del pop under', 'yes');
	add_option("POPUnder_intervallo_ora", '00', 'Intervallo in ore', 'yes');
	add_option("POPUnder_intervallo_minuto", '10', 'Intervallo in minuti', 'yes');
	add_option("POPUnder_attivo_home", '0', 'Attivato in home', 'yes');
	add_option("POPUnder_exit_text", 'close', 'Exit text button', 'yes');
	
}

if ((isset($_GET['activate'])) && ($_GET['activate']=='true')) {
	POPUnder_install();
}


?>
