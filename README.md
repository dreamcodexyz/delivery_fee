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

window.OneStep.$(document).on('click', '.ui-datepicker-next', function () {
    if(!window.OneStep.$(this).hasClass('ui-state-disabled')){
        window.OneStep.$('#extra_shipping_for_delivery_date').clone().insertBefore('.ui-datepicker-header');
    }
});

window.OneStep.$(document).on('click', '.ui-datepicker-prev', function () {
    if(!window.OneStep.$(this).hasClass('ui-state-disabled')){
        window.OneStep.$('#extra_shipping_for_delivery_date').clone().insertBefore('.ui-datepicker-header'); 
    }
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
});


```

#5 find " OneStep.Views.Init              = Backbone.View.extend({ " add this code to end of function initialize 

```
window.OneStep.$(".mw-osc-checkoutcontainer .mw-osc-column-2").appendTo("form#onestep_form");
window.OneStep.$(".col-main .review").appendTo("form#onestep_form");
```

#6 add this code before ` getAmPm: function(d){ `

```
        dynamicCSSRules: [],
        addCSSRule: function(rule) {
            var view = this;
            if ($.inArray(rule, view.dynamicCSSRules) == -1) {
                jQuery('head').append('<style>' + rule + '</style>');
                view.dynamicCSSRules.push(rule);
            }
        },
        updateDatePickerCells: function() {
            var view = this;
            /* Wait until current callstack is finished so the datepicker
               is fully rendered before attempting to modify contents */
            setTimeout(function () {
                //Fill this with the data you want to insert (I use and AJAX request).  Key is day of month
                //NOTE* watch out for CSS special characters in the value

                //Select disabled days (span) for proper indexing but // apply the rule only to enabled days(a)
                jQuery('.ui-datepicker td > *').each(function (idx, elem) {

                    var day   = window.OneStep.$(this).text();
                    var month = window.OneStep.$(this).parent().data('month');
                    var year  = window.OneStep.$(this).parent().data('year');
                    var delivery_day_current_hover = day + '/' + (month+1) + '/' + year;
                    var delivery_day_current_hover_for_css = day + '_' + (month+1) + '_' + year;
                    var delivery_day_current_hover_formated =  (month+1) + '/' + day + '/' + year;
                    var extra_fee = view.getExtraFeeByDate(delivery_day_current_hover, delivery_day_current_hover_formated);
                    
                    // dynamically create a css rule to add the contents //with the :after                         
                    // selector so we don't break the datepicker //functionality 
                    var className = 'datepicker-content-' + delivery_day_current_hover_for_css; // + '-' + extra_fee;
                    
                    var extra_fee_text = '+'+window.onestepConfig.delivery.current_currency_code + ' ' + extra_fee;

                    view.addCSSRule('.ui-datepicker td a.' + className + ':after {content: "' + extra_fee_text + '";}');
                    view.addCSSRule('.ui-datepicker td span.' + className + ':after {content: "' + extra_fee_text + '";}');
                    // }    
                    jQuery(this).addClass(className);
                    jQuery(this).attr('data-fee',extra_fee);
                });
            }, 0);
        },
        getExtraFeeByDate: function(current_date, current_date_formated){
            var extra_fee = 0;
            if(window.onestepConfig.delivery.fee_same_day == 1 && window.onestepConfig.delivery.current_day == current_date){
                extra_fee = Math.max(extra_fee, window.onestepConfig.delivery.fee_for_same_day) ; 
            } 

            if(window.onestepConfig.delivery.fee_next_day == 1 && window.onestepConfig.delivery.next_day == current_date){
                extra_fee = Math.max(extra_fee, window.onestepConfig.delivery.fee_for_next_day) ;
            } 

            var configValue = jQuery.parseJSON(window.onestepConfig.delivery.special_days);
            jQuery.each(configValue, function(i, item) {
                if(current_date_formated == configValue[i].date){
                    extra_fee = Math.max(extra_fee, configValue[i].fee) ;
                }
            }); 
            return extra_fee;
        },
```


#7 find 

```
this.option = {
    showAnim: 'fadeIn',
    duration:'fast',
    showOn: 'button',
    ...
```

replate with:

```

    this.option = {
        showAnim: 'fadeIn',
        duration:'fast',
        showOn: 'button',
        buttonImage: onestepConfig.delivery.buttonImage,
        minDate:this.isNowDay,
        buttonImageOnly: true,
        dateFormat: this.formatDatePicker,
        beforeShowDay: function(date){
            return view.noWeekendsOrHolidays(date);
        },
        // onSelect: function (date, dp) {
        //     view.updateDatePickerCells();
        // },
        // onChangeMonthYear: function(month, year, dp) {
        //     view.updateDatePickerCells();
        // },
        beforeShow: function(elem, dp) { //This is for non-inline datepicker
            view.updateDatePickerCells();
        }
    };


```


# Open and add this code to end of file: "/app/design/frontend/default/ma_flower/template/mw_onestepcheckout/daskboard.phtml"

```
<style>
div.ui-datepicker{
        font-size:16px;
    }
    .ui-datepicker td span,
    .ui-datepicker td a{
        font-size: 12px;
    }

    .ui-datepicker td span:after,
    .ui-datepicker td a:after
    {
        content: "";
        display: block;
        font-size: 10px;
        width: 30px;
        overflow: hidden;
        margin: 0;
        padding: 0;
        text-align: right;
        color: green;
        direction: ltr;
    }
    .ui-datepicker td{
        position: relative;
    }

    .ui-state-default, .ui-widget-content .ui-state-default{
        border: none;
    }

    .ui-datepicker td{
        background: #5ba8e9;
        border: 1px solid #cccccc;
    }
    .ui-datepicker-other-month, .ui-widget-content .ui-datepicker-other-month{
        background: transparent;
    }

</style>

<div style="display: none;">
    <div id="extra_shipping_for_delivery_date" style="overflow:auto;">

            <script type="text/javascript">
                <?php

            $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
            $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();

            echo "window.onestepConfig.delivery.fee_same_day = '" . Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_same_day') ."'; ";
            echo "window.onestepConfig.delivery.fee_for_same_day = '" . Mage::helper('directory')->currencyConvert(Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_for_same_day'), $baseCurrencyCode, $currentCurrencyCode) ."'; ";
            
            echo "window.onestepConfig.delivery.fee_next_day = '" . Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_next_day') ."'; ";
            echo "window.onestepConfig.delivery.next_day = '" . Date('d/m/Y', strtotime('+1 day')) ."'; ";
            echo "window.onestepConfig.delivery.fee_for_next_day = '" . Mage::helper('directory')->currencyConvert(Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/fee_for_next_day'), $baseCurrencyCode, $currentCurrencyCode) ."'; ";
            
            echo "window.onestepConfig.delivery.current_currency_code = '" . Mage::app()->getLocale()->currency( $currentCurrencyCode )->getSymbol() ."'; ";

            $special_days_data = unserialize(Mage::app()->getStore($scopeId)->getConfig('dcshippingrule/general/special_days'));
            if(!empty($special_days_data)){
                foreach ( $special_days_data as $key => $value) {
                    $special_days_data[$key]['fee'] = Mage::helper('directory')->currencyConvert($value['fee'], $baseCurrencyCode, $currentCurrencyCode);
                }
                echo "window.onestepConfig.delivery.special_days = '" . json_encode($special_days_data) ."'; ";
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

# Demo https://prnt.sc/h9fhvw