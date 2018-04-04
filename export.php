<?php
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

try {
	ob_start ();
	?>
<style>
*{
    margin: 0;
    box-sizing: border-box;
    padding: 5;
}
.content{
	margin: 60px;
}

h4 {
	color: red;
    /*margin: 10px 0 0 0;  */
}

ol { 
      margin: 0 0 0 0;
    padding: 10px 0px 10px 0px;
    border-bottom: 1px solid #ddd;
}
li {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    width: 80%;
    margin: auto;
}
</style>
<?php
	$db = new Database ();
	
	$questions = $db->get ( Help::QUESTIONS_TABLE );
	echo '<div class="content">';
	
	foreach ( $questions as $question ) {
		$qid = $question ['id'];
		$db->where ( " que_id = " . $qid );
		$answers = $db->get ( Help::ANSWERS_TABLE );
		
		$questionBy = Help::getBulkAdminDetails ( $question ['admin_id'] );
		
		echo '<h4>' . strip_tags($question ['question']) .' (' . $questionBy ['nickname'] . ')</h4>';
		echo '<p><small>at <em>' . $question['time'] . '</em></small></p>';
		echo '<ol>';
		foreach ( $answers as $answer ) {
			$tmpUser = Help::getUserDetails ( $answer ['user_id'] );
			echo '<li>';
			echo '<span>' . strip_tags($answer ['answer']) . '</span>';
			if ($answer ['attachments'] != "") {
				echo ' <small>Attachment: <a href="' . $answer ['attachments'] . '">Download</a></small>';
			}
			echo '<p><small><em> By ' . $tmpUser ['username'] . " (at ". $answer ['time'] . ")</em></small></p>";
			
			
			echo '</li>';
		}
		
		echo '</ol>';
	}
	echo '</div>';
	$x = 0;
	if ($x) {
		
		echo $content = ob_get_clean ();
		die ();
	} else {
		$content = ob_get_clean ();
	}
	
	$html2pdf = new Html2Pdf ( 'P', 'A4', 'fr',true, 'UTF-8' );
	$html2pdf->setDefaultFont ( 'Arial' );
	$html2pdf->writeHTML ( utf8_encode($content ));
	$html2pdf->output ( 'exemple00.pdf' );
} catch ( Html2PdfException $e ) {
	$formatter = new ExceptionFormatter ( $e );
	echo $formatter->getHtmlMessage ();
}