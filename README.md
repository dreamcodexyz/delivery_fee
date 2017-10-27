# delivery_fee
delivery_fee

Open file: app/code/local/MW/Onestepcheckout/controllers/IndexController.php
Add this code below "if($post) {"

Mage::dispatchEvent('controller_action_postdispatch_onestepcheckout_index_save', array('post' => $post, 'controller_action' => $this));