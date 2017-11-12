# delivery_fee
delivery_fee

# Open file: "app/code/local/MW/Onestepcheckout/controllers/IndexController.php"

#1 Add this code after ` if($post) { `
```
Mage::dispatchEvent('controller_action_postdispatch_onestepcheckout_index_save', array('post' => $post, 'controller_action' => $this));
```


# Open file "js/mw_onestepcheckout/view.js"

#1 Add this code before "OneStep.Views.Shipping  = Backbone.View.extend({" :
```
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
```

#2 Add this code after "view_onestep_shipping_method        = new OneStep.Views.ShippingMethod();": 
```
view_onestep_deliverytime_method    = new OneStep.Views.DeliverytimeMethod();
```
#3 Add this code after "view_onestep_shipping_method,":
```
view_onestep_deliverytime_method,
```

#4 find "OneStep.Views.DeliveryDate      = Backbone.View.extend({" add this code before `this.editTimepicker();`

```
window.OneStep.$(document).find(window.OneStep.$('#onestepcheckout_date')).next().datepicker().on('click', function() {
    window.OneStep.$('#extra_shipping_for_delivery_date').clone().insertBefore('.ui-datepicker-header');
});

window.OneStep.$(document).on('mouseenter', 'td[data-handler="selectDay"]', function() {
    var day   = window.OneStep.$(this).text();
    var month = window.OneStep.$(this).data('month');
    var year  = window.OneStep.$(this).data('year');
    var delivery_day_current_hover = day + '/' + (month+1) + '/' + year;
    window.OneStep.$(".ui-datepicker #dc_curent_delivery_date").text( delivery_day_current_hover );
    var extra_fee = 0;

    if(window.onestepConfig.delivery.fee_same_day == 1 && window.onestepConfig.delivery.current_day == delivery_day_current_hover){
        extra_fee = Math.max(extra_fee, window.onestepConfig.delivery.fee_for_same_day) ; 
    } 

    if(window.onestepConfig.delivery.fee_next_day == 1 && window.onestepConfig.delivery.next_day == delivery_day_current_hover){
        extra_fee = Math.max(extra_fee, window.onestepConfig.delivery.fee_for_next_day) ;
    } 

    var configValue = jQuery.parseJSON(window.onestepConfig.delivery.special_days);
    var delivery_day_current_hover_formated =  (month+1) + '/' + day + '/' + year;
    jQuery.each(configValue, function(i, item) {
        if(delivery_day_current_hover_formated == configValue[i].date){
            extra_fee = Math.max(extra_fee, configValue[i].fee) ;
        }
    });
    
    window.OneStep.$(".ui-datepicker #extra_shipping_fee_for_delivery_date").text( extra_fee );
})


```

#5 add this code to end of file: "/app/design/frontend/default/ma_flower/template/mw_onestepcheckout/daskboard.phtml"

```
<div style="display: none;">
    <div id="extra_shipping_for_delivery_date" style="overflow:auto;">

        <script type="text/javascript">
            <?php
        echo "window.onestepConfig.delivery.fee_same_day = '" . Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_same_day') ."'; ";
        echo "window.onestepConfig.delivery.fee_for_same_day = '" . Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_for_same_day') ."'; ";
        echo "window.onestepConfig.delivery.fee_next_day = '" . Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_next_day') ."'; ";
        echo "window.onestepConfig.delivery.next_day = '" . Date('d/m/Y', strtotime('+1 day')) ."'; ";
        echo "window.onestepConfig.delivery.fee_for_next_day = '" . Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_for_next_day') ."'; ";

        $configValue = unserialize(Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/special_days'));
        if(!empty($configValue)){
            echo "window.onestepConfig.delivery.special_days = '" . json_encode($configValue) ."'; ";
            echo "window.onestepConfig.delivery.current_day = '" . date('d/m/Y', Mage::getModel('core/date')->gmtTimestamp()) ."'; ";
        }
            ?>
        </script>

        <h3><?php echo $this->__('Extra shipping fee for delivery date');?> </h3>
        <p>
            <span style="display: none;" id="dc_curent_delivery_date"><?php echo date('d/m/Y', Mage::getModel('core/date')->gmtTimestamp()); ?></span>
            <p><?php echo $this->__('Fee: '); ?><span id="extra_shipping_fee_for_delivery_date">0</span></p>
        </p>
        
    </div>
</div>

```


# Use:
Go to System configurations > Dreamcode Extension > Delivery Shipping Rule