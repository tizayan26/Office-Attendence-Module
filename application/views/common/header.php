<header>
    <?php echo heading(img(base_url('assets/images/logo.png')).$this->lang->line('title'));?>
    <?php if(isset($session) && $session['type']!= 'Administrator'):?><div style="float:right;font-family: tahoma,arial,verdana,sans-serif;">User:&nbsp;<?php echo $session['fullname']?></div><?php endif;?>
</header>