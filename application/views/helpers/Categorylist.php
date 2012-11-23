<?php

class Zend_View_Helper_categorylist extends Zend_View_Helper_Abstract
{
    
    public function handlelogin($username)
    {
    	//
    	//$login=$this->translate('Log in');
    	if (!isset($username))
    	
        $ctext = '<div class="sidebar_box_border" style="border:solid 1px #CCCCCC">
                	<div class="sidebar_box">
                	<span class="sidebar_title">Members login</span>
                    <img src="/images/user-icon.png" width="37" height="27" alt="user" title="user"/>
                    <form name="login" action="/en/perdorues/submit" method="POST">
                    	<div id="login">
                        <label>Username:</label><input type="text" name="username" class="login_input" />
		      				<div class="clear10"></div>
		       			<label>Password:</label><input type="password" name="password"  class="login_input"/>
                       		 <div class="clear10"></div>
		      		 	<label>&nbsp;</label><input name="" type="submit" value="Login" class="search_submit"/>
                       		<div class="clear10"></div>
                            <span>&nbsp;&nbsp;&nbsp;Do not have an account?</span> <a href="/en/info/regjistrohu" class="regjistrohu">Sign up</a>
                        </div>
                     </form>
                    </div> 
                </div>';
        return $ctext;
    }
 
    
}