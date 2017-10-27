# delivery_fee
delivery_fee

# Open file: "app/code/local/MW/Onestepcheckout/controllers/IndexController.php", add this code after "if($post) {"

Mage::dispatchEvent('controller_action_postdispatch_onestepcheckout_index_save', array('post' => $post, 'controller_action' => $this));


# Use:
Go to System configurations > Dreamcode Extension > Delivery Shipping Rule


# Open file "js/mw_onestepcheckout/view.js"

Add this code before "OneStep.Views.Shipping  = Backbone.View.extend({" :

OneStep.Views.DeliverytimeMethod    = Backbone.View.extend({
    el: window.OneStep.$("#delivery_date_load"),
    events: {
        "change  #onestepcheckout_date"                : "hdlChangeMethod"
        // "change  #onestepcheckout_offset"                : "hdlChangeMethod"
    },
    initialize: function(){
        this.change_onestepcheckout_date    = -1;
    },
    hdlChangeMethod: function(ev){
        var val = window.OneStep.$(ev.target).val();
        if(val != this.change_onestepcheckout_date){
            if(onestepConfig.ajaxShipping){
                var params = {updateshippingmethod: false, updatepaymenttype: (onestepConfig.ajaxPaymentOnShipping ? true : false)}; /* false: mean dont show loading on block shipping method */
                view_onestep_init.update(params);
                this.change_onestepcheckout_date = val;
            }
        }
    }
});


add this code after "view_onestep_shipping_method        = new OneStep.Views.ShippingMethod();": 

view_onestep_deliverytime_method    = new OneStep.Views.DeliverytimeMethod();

add this code after "view_onestep_shipping_method,":

view_onestep_deliverytime_method,