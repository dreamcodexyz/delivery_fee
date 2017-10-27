# delivery_fee
delivery_fee

Add 

Mage::dispatchEvent('controller_action_postdispatch_onestepcheckout_index_save', array('post' => $post, 'controller_action' => $this));

below if($post) { in app/code/local/MW/Onestepcheckout/controllers/IndexController.php