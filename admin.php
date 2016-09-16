<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "info@collinreymondsalon.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "6f7dd2" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'7227' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUNDkEVbWVsZHR0aRFDERBpdGwJQxaYwNDoAxQKQ3Re1aumqlVkrs5Dcx+gAVNkKhEh6WRsYAoCiU5DFREAqA4AQSSwAqJLRASiOIiYa6hoaiCI2UOFHRYjFfQDTtcrWsnEDoAAAAABJRU5ErkJggg==',
			'0479' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM3QMQ6AIAwF0M/ADfA+OLjXAQdOU4feAI7AwiklJCY0Omq03X7S9qWol2L8qV/xGQ+xgbIfMkvIYCIaMpcQwKt3Q0ZiFuzzmXVSLKXUUuM2+EicICHr2Sl4anv1DWkedaNZxDKUpZsZyvzV/x7sG98BBCTLHqmg8sIAAAAASUVORK5CYII=',
			'0593' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1EQxmA0AFJjDVApIHR0dEhAElMZIpIA2tDQIMIklhAq0gISCwAyX1RS6cuXZkZtTQLyX0BrQyNDiFwdQgxNPOAdjQ6oomxBrC2oruF0YExBN3NAxV+VIRY3AcAaJvMb9z2mBMAAAAASUVORK5CYII=',
			'6FFC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DA6YGIImJTBFpYG1gCBBBEgtoAYkxOrAgizVAxJDdFxk1NWxp6MosZPeFTEFRB9HbilsM2Q5sbmENAIuhuHmgwo+KEIv7AIR2yst2K180AAAAAElFTkSuQmCC',
			'074D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQx0aHUMdkMRYAxgaHVodHQKQxESmAMWmOjqIIIkFtDK0MgTCxcBOilq6atrKzMysaUjuA6oLYG1E18vowBoaiCImMoW1gQFNHWuACFgM2S2MDmAxFDcPVPhREWJxHwB+dsuFVDcWvQAAAABJRU5ErkJggg==',
			'0C5A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDHVqRxVgDWBtdGximOiCJiUwRaQCKBQQgiQW0ijSwTmV0EEFyX9TSaauWZmZmTUNyH0gdQ0MgTB2yWGgIhh2o6kBucXR0RBEDuZkhlBFFbKDCj4oQi/sAc2jLb6VChtEAAAAASUVORK5CYII=',
			'912D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjAGMDo6OgQgiQW0sgawNgQ6iKCIAfUixMBOmjZ1VdSqlZlZ05Dcx+oKVNfKiKKXAaR3CqqYAEgsAFVMZApIhBHFLUCXhLKGBqK4eaDCj4oQi/sAAGrH5SvwjsMAAAAASUVORK5CYII=',
			'268C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaYGIImJTGFtZXR0CBBBEgtoFWlkbQh0YEHW3SrSwOjo6IDivmnTwlaFrsxCcV+AaCuSOjBkdBBpdAWah+KWBogYsh0iDZhuCQ3FdPNAhR8VIRb3AQB4mMoZcprzIgAAAABJRU5ErkJggg==',
			'D00D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgMYAhimMIY6IIkFTGEMYQhldAhAFmtlbWV0dHQQQRETaXRtCISJgZ0UtXTaytRVkVnTkNyHpg6PGBY7sLgFm5sHKvyoCLG4DwBcjcxVCeG+zQAAAABJRU5ErkJggg==',
			'C8C7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WEMYQxhCHUNDkMREWllbGR0CGkSQxAIaRRpdGwRQxRpYW1nBNMJ9UatWhi1dtWplFpL7oOpaGVD0gsxjmMKAaUcAA4ZbAh2wuBlFbKDCj4oQi/sAe+3MM+83wq0AAAAASUVORK5CYII=',
			'FAB3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGUIdkMQCGhhDWBsdHQJQxFhbWYGkCIqYSKNro0NDAJL7QqOmrUwNXbU0C8l9aOqgYqKhrtjMw2oHuluAYmhuHqjwoyLE4j4APlLPy62BlXsAAAAASUVORK5CYII=',
			'BE5F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHUNDkMQCpog0sDYwOiCrC2jFIgZSNxUuBnZSaNTUsKWZmaFZSO4DqWNoCMQwD5sYK7oYUC+joyOKGMjNDKGobhmo8KMixOI+AFCxypBoFyuWAAAAAElFTkSuQmCC',
			'D914' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYQximMDQEIIkFTGFtZQhhaEQRaxVpdAxhaEUXc5jCMCUAyX1RS5cuzZq2KioKyX0BrYyBDlMYHVD1MgD1MoaGoIixgMzDdAuaGMjNjKEOKGIDFX5UhFjcBwAfo89pzpfn8wAAAABJRU5ErkJggg==',
			'7273' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA0IdkEVbWVsZGgIdAlDERBodGgIaRJDFpjA0OoBFkdwXtWopCGYhuY/RAahyCkMDsnmsDQwBQIhinghQJVAtilgAUCUrUDwARUw01LWBAdXNAxR+VIRY3AcA5k3Mk+5H+30AAAAASUVORK5CYII=',
			'FC7E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1lDA0MDkMQCGlgbHRoCHRhQxEQasIkxNDrCxMBOCo2atmrV0pWhWUjuA6ubwoipNwBTzNEBXYy10bUBXQzoZiBGdvNAhR8VIRb3AQA+aMwEGuc6pwAAAABJRU5ErkJggg==',
			'1597' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM3QsRGAMAhA0VBkg2QfsgEFadzATEGKbKAjWMQpjR2clnoK3Wv4h9svI+5P+0ofYMwuQ2ZlHoNAQgnK4jAvZAww8Gmk+npZtz5Pvag+QFeRqdm7w4QWa6EmIbLmG6SE2iIDj2ZjX/3vwb3pOwBcVcj66VeG1AAAAABJRU5ErkJggg==',
			'196D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYHVhbGR0dHQKQxEQdRBpdGxwdRFD0gsQYYWJgJ63MWro0derKrGlI7gPaEejqiK6XAag3EE2MBYsYFreEYLp5oMKPihCL+wBwJMhz7XhliAAAAABJRU5ErkJggg==',
			'D056' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHaY6IIkFTGEMYW1gCAhAFmtlbWVtYHQQQBETaXSdyuiA7L6opdNWpmZmpmYhuQ+kzqEhEM08sJiDCIYdaGJAtzA6OqDoBbmZIZQBxc0DFX5UhFjcBwAf3sz4z4m9ewAAAABJRU5ErkJggg==',
			'73B5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNZQ1hDGUMDkEVbRVpZGx0dUFS2MjS6NgSiik1hAKlzdUB2X9SqsKWhK6OikNzH6ABS59AggqSXtQFkXgCKmEgDxA5kMaAKkN6AABQxkJsZpjoMgvCjIsTiPgCV2MwEqp+GEAAAAABJRU5ErkJggg==',
			'3513' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7RANEQxmmMIQ6IIkFTBFpYAhhdAhAVtkq0sAYwtAggiw2RSQEqLchAMl9K6OmLl01bdXSLGT3TWFodECog5oHERNBtQNDLGAKayvDFFS3iAYwhjCGOqC4eaDCj4oQi/sAOgTMWbDP/foAAAAASUVORK5CYII=',
			'09FB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA0MdkMRYA1hbWYEyAUhiIlNEGl2BYiJIYgGtELEAJPdFLV26NDV0ZWgWkvsCWhkDXdHMC2hlwDBPZAoLhhg2t4Dd3MCI4uaBCj8qQizuAwAThMpyRq/DFAAAAABJRU5ErkJggg==',
			'4A48' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjAEMDQ6THVAFgthDGFodQgIQBJjDGFtZZjq6CCCJMY6RaTRIRCuDuykadOmrczMzJqaheS+AKA610ZU80JDRUNdQwNRzGMAmdfoiEUMVS9UDNXNAxV+1INY3AcAw8jN267CP2UAAAAASUVORK5CYII=',
			'2312' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsQ3AIAwETcEGHgg2cGFSZBoo2MDJEEwZh+qlpEwk/HLByZZP0HhUpZXyi1+UqGR0JGBs3ElJBJh0allDYtzuHvN59DvH5j129JM51/BGSP425+hSJzNkXPneFWSlRA0lF13g/z7Mi98F9Q7LPi526UsAAAAASUVORK5CYII=',
			'1AA3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YAhimMIQ6IImxOjCGMIQyOgQgiYk6sLYyOjo0iKDoFWl0bQhoCEBy38qsaStTV0UtzUJyH5o6qJhoqGtoAFbzMMUCUd0SAlaH4uaBCj8qQizuAwCi8cuGiCczZQAAAABJRU5ErkJggg==',
			'2EEA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHVqRxUSmiDSwNjBMdUASC2gFiwUEIOsGizE6iCC7b9rUsKWhK7OmIbsvAEUdGIJ4QLHQEGS3NGCqE8EiFhoKcrMjithAhR8VIRb3AQBo/cnIuOsK7wAAAABJRU5ErkJggg==',
			'8AA6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEx1QBITmcIYwhDKEBCAJBbQytrK6OjoIICiTqTRtSHQAdl9S6OmrUxdFZmaheQ+qDo080RDXUMDHURQxCDmiWDYEYCilzUALIbi5oEKPypCLO4DADIPzZDUPjxkAAAAAElFTkSuQmCC',
			'5922' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAe0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGaY6IIkFNLC2Mjo6BASgiIk0ujYEOoggiQUGiDQ6gGSQ3Bc2benSrJVZq6KQ3dfKGOjQytCIbAcDiD8FSCLb0crS6BDAMAVZTGQK0C0ODAHIYqwBjCGsoYGhIYMg/KgIsbgPAKtpzErm/tQnAAAAAElFTkSuQmCC',
			'5C2D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMYQxlCGUMdkMQCGlgbHR0dHQJQxEQaXBsCHUSQxAIDQDy4GNhJYdOmrVq1MjNrGrL7WoEqWhlR9ILFpqCKBQDFHAJQxUSmAN3iwIjiFtYAxlDW0EAUNw9U+FERYnEfAGvKy1y1N8cyAAAAAElFTkSuQmCC',
			'6A0B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIY6IImJTGEMYQhldAhAEgtoYW1ldHR0EEEWaxBpdG0IhKkDOykyatrK1FWRoVlI7guZgqIOordVNBQkhmJeq0ijI5odIkC9DmhuYQ0AiqG5eaDCj4oQi/sA/5nMWmqM9IcAAAAASUVORK5CYII=',
			'9739' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WANEQx1DGaY6IImJTGFodG10CAhAEgtoZWh0aAh0EEEVA4o6wsTATpo2ddW0VVNXRYUhuY/VlSEAqHsqsl6GVkYHoAkNyGICrawgU1HsEJki0sCK5hbWAJEGRjQ3D1T4URFicR8A4Q/MlW9Ll6sAAAAASUVORK5CYII=',
			'D6CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhCHaYGIIkFTGFtZXQICBBBFmsVaWRtEHRgQRVrYG1gdEB2X9TSaWFLV63MQnZfQKtoK5I6uHmuWMXQ7MDiFmxuHqjwoyLE4j4Al4XMjFXjHBMAAAAASUVORK5CYII=',
			'35F6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RANEQ1lDA6Y6IIkFTBFpYG1gCAhAVtkKEmN0EEAWmyISAhJDdt/KqKlLl4auTM1Cdt8UhkbXBkY088BiDiKodmCIBUxhbUV3i2gAI9BeBhQ3D1T4URFicR8AvCXLDO3fm8cAAAAASUVORK5CYII=',
			'7442' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMZWhkaHaY6IIu2MkxlaHUICEAVC2WY6ugggiw2hdGVIdChQQTZfVFLl67MzFoVheQ+RgeRVtZGh0ZkO1gbRENdQwNakd0i0gB2yxRksQCIWACmmGNoyCAIPypCLO4DALRKzLNRJA6kAAAAAElFTkSuQmCC',
			'B2AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QgMYQximMIY6IIkFTGFtZQhldAhAFmsVaXR0dHQQQVHH0OjaEAhTB3ZSaNSqpUtXRYZmIbkPqG4KK0Id1DyGANbQQFTzWhkdQOpQ7WBtQNcbGiAaCrQXxc0DFX5UhFjcBwCces1/cMQTfgAAAABJRU5ErkJggg==',
			'2929' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAe0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEHWDRRzQIhB3DRt6dKslVlRYcjuC2AMdGhlmIqsl9GBodFhCtAuZLc0sDQ6BDCg2CHSAHSLAwOKW0JDGUNYQwNQ3DxQ4UdFiMV9AO0qyyJniK2vAAAAAElFTkSuQmCC',
			'CE9D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WENEQxlCGUMdkMREWkUaGB0dHQKQxAIaRRpYGwIdRJDFGlDEwE6KWjU1bGVmZNY0JPeB1DGEYOplQDcPaAcjmhg2t2Bz80CFHxUhFvcBAMNUyw+AhXhiAAAAAElFTkSuQmCC',
			'E50F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMIaGIIkFNIg0MIQyOjCgiTE6OqKLhbA2BMLEwE4KjZq6dOmqyNAsJPcFNDA0uiLU4RETaXTEsIO1Fd0toSGMIUA3o4gNVPhREWJxHwCWO8rUdqqArAAAAABJRU5ErkJggg==',
			'D571' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDA1qRxQKmiIDIqShirWCxUDSxEIZGB5hesJOilk5dugoEkdwHNL3RYQoDqh0gsQB0MZFGRwc0sSmsrawNqGKhAYwhQLHQgEEQflSEWNwHAM2Ozh7JeZaMAAAAAElFTkSuQmCC',
			'6EE7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHUNDkMREpog0sIJoJLGAFixiDRCxACT3RUZNDVsaumplFpL7QiDmtSLbG9AKFpuCRSwAWQziFkYHLG5GERuo8KMixOI+ABYmyxSmeNg8AAAAAElFTkSuQmCC',
			'F2CE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHUMDkMQCGlhbGR0CHRhQxEQaXRsE0cQYgGKMMDGwk0KjVi1dumplaBaS+4DqprAi1MHEAjDFGB1YMewAqUJ3i2ioA5qbByr8qAixuA8AyqjLC1SboocAAAAASUVORK5CYII=',
			'52BB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGUMdkMQCGlhbWRsdHQJQxEQaXRsCHUSQxAIDGBpdEerATgqbtmrp0tCVoVnI7mtlmIJuHlAsgBXNvIBWRgd0MZEprA3oelkDRENd0dw8UOFHRYjFfQBoacwxTK3MwQAAAABJRU5ErkJggg==',
			'66E7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHUNDkMREprC2soJoJLGAFpFGDDEgjxVMI9wXGTUtbGnoqpVZSO4LmSIKMq8V2d6AVpFG1waGKVjEApDFIG5hdMDiZhSxgQo/KkIs7gMAYF7LYEtTkQ0AAAAASUVORK5CYII=',
			'0359' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDHaY6IImxBoi0sjYwBAQgiYlMYWh0BaoWQRILaGVoZZ0KFwM7KWrpqrClmVlRYUjuA6kDklPR9DY6NAQ0iGDYEYBiB8gtjI4OKG4BuZkhlAHFzQMVflSEWNwHAPeay025YSU+AAAAAElFTkSuQmCC',
			'DD11' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QgNEQximMLQiiwVMEWllCGGYiiLWKtLoGMIQii7mgNALdlLU0mkrs6atWorsPjR1pImB3IImBnIzY6hDaMAgCD8qQizuAwCr9M5OjH6E8QAAAABJRU5ErkJggg==',
			'8DAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQximMIY6IImJTBFpZQhldAhAEgtoFWl0dHR0EEFV1+jaEAhTB3bS0qhpK1NXRYZmIbkPTR3cPNfQQBTzwGINgeh2tLKi6QW5GSiG4uaBCj8qQizuAwBM381TKs9KzgAAAABJRU5ErkJggg==',
			'836A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANYQxhCGVqRxUSmiLQyOjpMdUASC2hlaHRtcAgIQFHH0MrawOggguS+pVGrwpZOXZk1Dcl9YHWOjjB1SOYFhoZgiqGog7gFVS/EzYwoYgMVflSEWNwHAJh7y5zZW3BfAAAAAElFTkSuQmCC',
			'B3B5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDGUMDkMQCpoi0sjY6OiCrC2hlaHRtCEQVm8IAUufqgOS+0KhVYUtDV0ZFIbkPos6hQQTDvAAsYoEOIhhucQhAdh/EzQxTHQZB+FERYnEfAOpOzcZILI51AAAAAElFTkSuQmCC',
			'CC1C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WEMYQxmmMEwNQBITaWVtdAhhCBBBEgtoFGlwDGF0YEEWawCqmMLogOy+qFXTVq2atjIL2X1o6nCLAe1wmIJqB9gtU1DdAnIzY6gDipsHKvyoCLG4DwB+g8u0qXcwfAAAAABJRU5ErkJggg==',
			'8F73' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DA0IdkMREpogAyUCHACSxgFaQWECDCLq6RoeGACT3LY2aGrZq6aqlWUjuA6ubwtCAYV4AA4p5IDFGBwYMO1iBosh6WQNAYgwobh6o8KMixOI+AL+ezTLb9WhxAAAAAElFTkSuQmCC',
			'0D4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQxgaHUMdkMRYA0RaGVodHQKQxESmiDQ6THV0EEESC2gFigXC1YGdFLV02srMzMzQLCT3gdS5NqKaBxYLDUQxD2xHI6odYLeg6cXm5oEKPypCLO4DANJgzLmYkV4BAAAAAElFTkSuQmCC',
			'8937' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUNDkMREprC2sjY6NIggiQW0igBFAlDERKYAxcCiCPctjVq6NGvqqpVZSO4TmcIYCFTXyoBiHgNI5xRUMRaQWAADhlscHbC4GUVsoMKPihCL+wDjRc1QhYnHBwAAAABJRU5ErkJggg==',
			'79E5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHUMDkEVbWVtZGxgdUFS2ijS6ootNAYu5OiC7L2rp0tTQlVFRSO5jdGAMdAXSIkh6WRsYGtHFRBpYwHYgiwU0gNzCEBCAIgZys8NUh0EQflSEWNwHAEe2yumpkZjjAAAAAElFTkSuQmCC',
			'425B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpI37pjCGsIY6hjogi4WwtrI2MDoEIIkxhog0ugLFRJDEWKcwNLpOhasDO2natFVLl2ZmhmYhuS9gCsMUhoZAFPNCQxkCQGIiqG5xYMUQA7rE0RFFL8MU0VCHUEZUNw9U+FEPYnEfAHctyt7YveZ6AAAAAElFTkSuQmCC',
			'D593' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNEQxmA0AFJLGCKSAOjo6NDALJYq0gDa0NAgwiqWAhILADJfVFLpy5dmRm1NAvJfQGtDI0OIXB1CDFM8xod0cWmsLaiuyU0gDEE3c0DFX5UhFjcBwBBPs6vAKWg/QAAAABJRU5ErkJggg==',
			'5A90' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGVqRxQIaGEMYHR2mOqCIsbayNgQEBCCJBQaINLo2BDqIILkvbNq0lZmZkVnTkN3XKtLoEAJXBxUTDXVoQBULAKpzRLNDZApQDM0trEB7HdDcPFDhR0WIxX0A85XM+WlrT3UAAAAASUVORK5CYII=',
			'B25E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHUMDkMQCprC2sjYwOiCrC2gVaXRFF5vC0Og6FS4GdlJo1KqlSzMzQ7OQ3AdUN4WhIRDNPIYATDFGB1Z0sSlAlzg6ooiFBoiGOoQyorh5oMKPihCL+wAUkss+/NqMFAAAAABJRU5ErkJggg==',
			'0122' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaY6IImxBjAGMDo6BAQgiYlMYQ1gbQh0EEESC2gF6m0IaBBBcl/U0lVRq1ZmAQmE+8DqWhkaHdD1TgGKotgBFAOJoriFIQDsRhQ3s4ayhgaGhgyC8KMixOI+AD6jyOwn4KpRAAAAAElFTkSuQmCC',
			'8A34' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGRoCkMREpjCGsDY6NCKLBbSytoJIVHUiQFUOUwKQ3Lc0atrKrKmroqKQ3AdR5+iAap5oqENDYGgIihhQHdAl6Ha4gkWR3SzS6Ijm5oEKPypCLO4DAF+sz+DYpk1SAAAAAElFTkSuQmCC',
			'45AF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37poiGMkxhDA1BFgsRaWAIZXRAVscIFGN0dEQRY50iEsLaEAgTAztp2rSpS5euigzNQnJfwBSGRleEOjAMDQWKhaKKMUwRwVDHMIW1lRVDjDEEQ2ygwo96EIv7AJLMyljy3PUrAAAAAElFTkSuQmCC',
			'B700' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMLQiiwVMYWh0CGWY6oAs1srQ6OjoEBCAqq6VtSHQQQTJfaFRq6YtXRWZNQ3JfUB1AUjqoOYxOmCKsTYwYtgBVIHmltAAoBiamwcq/KgIsbgPAOsTzXIGnJb/AAAAAElFTkSuQmCC',
			'D781' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGVqRxQKmMDQ6OjpMRRFrZWh0bQgIRRNrZXR0gOkFOylq6appq0JXLUV2H1BdAJI6qBijAytIBkWMtQFDbIpIA7re0ACRBoZQhtCAQRB+VIRY3AcA9lnNijQqQYYAAAAASUVORK5CYII=',
			'022E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYA1hbGR0dHZDViUwRaXRtCEQRC2hlaHRAiIGdFLV01dJVKzNDs5DcB1Q3haGVEV1vAMMURjQ7gPwAVDGgW0BuRBFjdBANdQ0NRHHzQIUfFSEW9wEAAtDIu69plkMAAAAASUVORK5CYII=',
			'E162' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGaY6IIkFNDAGMDo6BASgiLEGsDY4OoigiDEAxRgaRJDcFxq1Kmrp1FWropDcB1bn6NDogKE3oJUBU2wKuhjILahuZg1lCGUMDRkE4UdFiMV9AOUayyJR1c/EAAAAAElFTkSuQmCC',
			'7049' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMZAhgaHaY6IIu2MoYwtDoEBKCIsbYyTHV0EEEWmyLS6BAIF4O4KWrayszMrKgwJPcxOog0ugLtQNbL2gAUCw1oQBYTaQDa0eiAYkdAA9AtjahuAbIx3TxA4UdFiMV9ADELzF245GRDAAAAAElFTkSuQmCC',
			'3D46' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RANEQxgaHaY6IIkFTBFpZWh1CAhAVtkqAlTl6CCALDYFKBbo6IDsvpVR01ZmZmamZiG7D6jOtdERwzzX0EAHEXQ7Gh1RxMBuaUR1CzY3D1T4URFicR8AtT7Nc8RXSikAAAAASUVORK5CYII=',
			'49C4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpI37pjCGMIQ6NAQgi4WwtjI6BDQiizGGiDS6Ngi0IouxTgGJMUwJQHLftGlLl6auWhUVheS+gCmMga4NQBOR9IaGMgD1MoaGoLiFBWQHqlumgN2CJobFzQMVftSDWNwHACJNzdjCJzlGAAAAAElFTkSuQmCC',
			'863A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGVqRxUSmsLayNjpMdUASC2gVaQSSAQEo6kQaGBodHUSQ3Lc0alrYqqkrs6YhuU9kimgrkjq4eQ4NgaEhmGIo6iBuQdULcTMjithAhR8VIRb3AQC5rMyJlXSFaAAAAABJRU5ErkJggg==',
			'D053' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHUIdkMQCpjCGsDYwOgQgi7WytrICaREUMZFG16lAGsl9UUunrUzNzFqaheQ+kDoHoKoANL0gMREMO9DEgG5hdHREcQvIzQyhDChuHqjwoyLE4j4AkY3OGkUfYY8AAAAASUVORK5CYII=',
			'5257' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHUNDkMQCGlhbWYG0CIqYSKMrmlhgAEOj61SQHMJ9YdNWLV2ambUyC9l9rQxTgKpaUWxuZQgAik1BFgtoZXRgbQgIQBYTmcLawOjo6IAsxhogGuoQyogiNlDhR0WIxX0AIMnLtwfF5JcAAAAASUVORK5CYII=',
			'EE7A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDA1qRxQIaREDkVAdMsYAAdLFGRwcRJPeFRk0NW7V0ZdY0JPeB1U1hhKlDiAUwhoagiTE6YKpjbUAVA7sZTWygwo+KEIv7AM+9zEYN6XxlAAAAAElFTkSuQmCC',
			'27BB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQ11DGUMdkMREpjA0ujY6OgQgiQW0AsUaAh1EkHW3MrSyItRB3DRt1bSloStDs5DdF8AQwIpmHqMDowMrmnmsYIgqJgKE6HpDQ4FiaG4eqPCjIsTiPgB/k8uBmRk/SgAAAABJRU5ErkJggg==',
			'E3EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUUlEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDHUMDkMQCGkRaWRsYHRhQxBgaXTHFkNWBnRQatSpsaejK0Cwk96Gpw2ceFjFMt2Bz80CFHxUhFvcBAAgAynmnRRRcAAAAAElFTkSuQmCC',
			'84E6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYWllDHaY6IImJTGGYytrAEBCAJBbQyhDK2sDoIICijtEVJIbsvqVRS5cuDV2ZmoXkPpEpIq1AdWjmiYa6AvWKoNoBUociBnRLK7pbsLl5oMKPihCL+wCJFsr0Qkk7RwAAAABJRU5ErkJggg==',
			'0230' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB0YQxhDGVqRxVgDWFtZGx2mOiCJiUwRaXRoCAgIQBILaGVodGh0dBBBcl/U0lVLV01dmTUNyX1AdVMYEOpgYgEMDYEoYiJTGB0Y0OwAuqUB3S2MDqKhjmhuHqjwoyLE4j4AdG/MYrok7N4AAAAASUVORK5CYII=',
			'EE18' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMEx1QBILaBBpYAhhCAhAE2MMYXQQQVc3Ba4O7KTQqKlhq6atmpqF5D40dUhi2MzDawfczYyhDihuHqjwoyLE4j4AjhfMkDJdXNIAAAAASUVORK5CYII=',
			'E7A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMEx1QBILaGBodAhlCAhAE3N0dHQQQBVrZW0IdEB2X2jUqmlLV0WmZiG5D6guAKgOzTxGB9bQQAcRFDHWBpB5qGIiQLEAFL2hIWAxFDcPVPhREWJxHwDdus2nMH2m/wAAAABJRU5ErkJggg==',
			'9962' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bXB0EMEQA6pHct+0qUuXpk5dtSoKyX2sroyBro4Ojch2MLQyAPUGtCK7RaCVBSQ2hQGLWzDdzBgaMgjCj4oQi/sAeDPMSmn4nKAAAAAASUVORK5CYII=',
			'C6F8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA6Y6IImJtLK2sjYwBAQgiQU0ijSyNjA6iCCLNYg0IKkDOylq1bSwpaGrpmYhuS+gQRTTvAaRRld08xoxxbC5BezmBgYUNw9U+FERYnEfAOd5y/oNrvX7AAAAAElFTkSuQmCC',
			'C4E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYWllDHaY6IImJtDJMZW1gCAhAEgtoZAhlbWB0EEEWa2B0ZQWpR3Jf1KqlS5eGgmiE+wKAJgLVNTqg6BUNdW1gaGVAtQOkbgoDqltAYgGYbnYMDRkE4UdFiMV9ALKFy8dlagX+AAAAAElFTkSuQmCC',
			'444F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37pjC0MjQ6hoYgi4UwTGVodXRAVscYwhDKMBVVjHUKoytDIFwM7KRp05YuXZmZGZqF5L6AKSKtrI2oekNDRUNdQwMdsLiFPLGBCj/qQSzuAwBYI8nz2o2x8AAAAABJRU5ErkJggg==',
			'A4EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YWllDHaYGIImxBjBMZW1gCBBBEhOZwhDKClTNgiQW0MroChJDdl/UUiAIXZmF7L6AVpFWJHVgGBoqGuqKJhbQytCKaQdIDNUtYDE0Nw9U+FERYnEfAM1Yypkn65cjAAAAAElFTkSuQmCC',
			'711B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZAhimMIY6IIu2MgYwhDA6BKCIsQYwAsVEkMWmgPXC1EHcFAWE01aGZiG5j9EBRR0YsjZAxJDNE8EiFtCAqTeggTWUMdQR1c0DFH5UhFjcBwAL1MhDALVw6QAAAABJRU5ErkJggg==',
			'6A40' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHVqRxUSmMIYwtDpMdUASC2hhbWWY6hAQgCzWINLoEOjoIILkvsioaSszMzOzpiG5L2SKSKNrI1wdRG+raKhraCCaGNC8RlQ7RKaAxVDcwhoAFkNx80CFHxUhFvcBAFUVzjw/GCI6AAAAAElFTkSuQmCC',
			'A308' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1YQximMEx1QBJjDRBpZQhlCAhAEhOZwtDo6OjoIIIkFtDK0MraEABTB3ZS1NJVYUtXRU3NQnIfmjowDA1laHRtCEQ3D4sdmG4JaMV080CFHxUhFvcBAG/WzLJZfIu1AAAAAElFTkSuQmCC',
			'34C3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7RAMYWhlCHUIdkMQCpjBMZXQIdAhAVglUxdog0CCCLDaF0ZUVpB7JfSujli5dumrV0ixk900RaUVSBzVPNNQVSIug2tGKbgfQLa3obsHm5oEKPypCLO4DAAuZzB0lWiQvAAAAAElFTkSuQmCC',
			'FB99' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGaY6IIkFNIi0Mjo6BASgijW6NgQ6iKCpY0WIgZ0UGjU1bGVmVFQYkvtA6hhCAqai6W10AJFoYo4NARh2YLoF080DFX5UhFjcBwDXKM2i1xBvIQAAAABJRU5ErkJggg==',
			'FFFA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAT0lEQVR4nGNYhQEaGAYTpIn7QkNFQ11DA1qRxQIaRBpYGximOmCKBQRgiDE6iCC5LzRqatjS0JVZ05Dch6YOWSw0BLd5JIkNVPhREWJxHwBWRMwbz+hYpwAAAABJRU5ErkJggg==',
			'0B26' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaY6IImxBoi0Mjo6BAQgiYlMEWl0bQh0EEASC2gVaWUAiiG7L2rp1LBVKzNTs5DcB1bXyohiHlCs0WEKo4MImh0OAahiYLc4MKDoBbmZNTQAxc0DFX5UhFjcBwC9hssHUDGYvQAAAABJRU5ErkJggg==',
			'A787' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QsRGAMAhFocgGcZ+ksKeQxhGcghTZIOcGFjKllEQt9S7QvfvAO0AfJTBS/+KHaeLEyItjgaDknCQ6FhuUWahjVKGi5cj5rYfuynpuzs9yZLnq7zJjCkINun1BjFHPoqDJ3BnY/Aj/+7Bf/C4OWMwEbMDOjQAAAABJRU5ErkJggg==',
			'E09A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGVqRxQIaGEMYHR2mOqCIsbayNgQEBKCIiTS6NgQ6iCC5LzRq2srMzMisaUjuA6lzCIGrQ4g1BIaGoNnB2ICuDuQWRxQxiJsZUcQGKvyoCLG4DwBeAcwtY74ixQAAAABJRU5ErkJggg==',
			'4065' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37pjAEMIQyhgYgi4UwhjA6Ojogq2MMYW1lbUAVY50i0ujawOjqgOS+adOmrUydujIqCsl9ASB1jg4NIkh6Q0NBegNQxBimgOwIdEAVA7nFIQDFfWA3M0x1GAzhRz2IxX0AFIHK0vGanqoAAAAASUVORK5CYII=',
			'743E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZWhlDGUMDkEVbGaayNjo6MKCKhTI0BKKKTWF0ZUCog7gpaunSVVNXhmYhuY/RQaSVAc081gbRUAc080RAtqCJAd3Viu4WkBiGmwco/KgIsbgPAJxLykZn5nwHAAAAAElFTkSuQmCC',
			'1F89' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGaY6IImxOog0MDo6BAQgiYkCxVgbAoEksl6QOkeYGNhJK7Omhq0KXRUVhuQ+iDqHqeh6WRsCGrCIYbEDzS0hQBVobh6o8KMixOI+ANCDyMvbsmFnAAAAAElFTkSuQmCC',
			'A424' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGRoCkMRYAximMjo6NCKLiUxhCGVtCGhFFgtoZXQFklMCkNwXtXTp0lUrs6KikNwX0CrSytDK6ICsNzRUNNRhCmNoCIp5QLcEoLolAKwTU4w1NABFbKDCj4oQi/sAaUHNcnuBWR0AAAAASUVORK5CYII=',
			'5817' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMYQximMIaGIIkFNLC2MoQwNIigiIk0OqKJBQYA1U0BySHcFzZtZdiqaatWZiG7rxWsrhXF5laRRocpIN1IdkDEApDFRKaA9DI6IIuxBjCGMIY6oogNVPhREWJxHwC7DsuOL2THKgAAAABJRU5ErkJggg==',
			'C6C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WEMYQxhCHaY6IImJtLK2MjoEBAQgiQU0ijSyNgg6iCCLNYg0sDYwwNSBnRS1alrY0lWrpmYhuS+gQbQVSR1Mb6NrAyOqeY0gMVQ7sLkFm5sHKvyoCLG4DwCGTsyC8ZT7yAAAAABJRU5ErkJggg==',
			'AC50' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDHVqRxVgDWBtdGximOiCJiUwRaQCKBQQgiQW0ijSwTmV0EEFyX9TSaauWZmZmTUNyH0gdQ0MgTB0YhoZiioHUuTYEoNnB2ujo6IDiloBWxlCGUAYUNw9U+FERYnEfAA1CzSumrimhAAAAAElFTkSuQmCC',
			'CBDA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WENEQ1hDGVqRxURaRVpZGx2mOiCJBTSKNLo2BAQEIIsBVbI2BDqIILkvatXUsKWrIrOmIbkPTR1MDGheYGgIhh2o6iBucUQRg7iZEUVsoMKPihCL+wCzUM1DRasPFgAAAABJRU5ErkJggg==',
			'A68B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDSyNgQ6iCCJBbSKNCCpAzspaum0sFWhK0OzkNwX0CqKYV5oqEijK6Z5WMQw3RLQiunmgQo/KkIs7gMA0jjLebr69TwAAAAASUVORK5CYII=',
			'8FD9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DGaY6IImJTBFpYG10CAhAEgtoBYo1BDqIoKtDiIGdtDRqatjSVVFRYUjug6gLmCqCYV5AAxYxTDvQ3MIaABRDc/NAhR8VIRb3AQBc980v4zmLPgAAAABJRU5ErkJggg=='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>