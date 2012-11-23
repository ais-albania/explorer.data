<?php

class Zend_View_Helper_Share extends Zend_View_Helper_Abstract
{
    public function share() {
		$share='
<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style "><a href="http://www.twitter.com/opendataalbania"><img src="http://twitter-badges.s3.amazonaws.com/t_mini-b.png" alt="Follow OpenDataAlbania on Twitter" target="_blank"/></a>
		<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="open.data.al" layout="button_count" show_faces="true" width="50" font=""></fb:like>
<a href="http://www.addthis.com/bookmark.php?v=250&amp;username=xa-4d5450781aab202a" class="addthis_button_compact">Share</a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4d5450781aab202a"></script>
<!-- AddThis Button END -->';		
		return $share;
	}
    
}
