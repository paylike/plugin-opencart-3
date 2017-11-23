<?php if($warning_test_mode){ ?>
	<div class="alert alert-info attention"><i class="fa fa-exclamation-circle"></i>&nbsp;<?php echo $warning_test_mode; ?></div>
<?php } ?>
<div data-amount="<?php echo $amount; ?>" style="float: left" id="paylike-payment-widget"></div>
<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
  </div>
</div>
<script type="text/javascript" src="https://sdk.paylike.io/3.js"></script>
<script type="text/javascript"><!--
	$('#button-confirm').on('click', function() {
		$('.paylikealert').remove();
		var paylike = Paylike('<?php echo $paylike_public_key; ?>');
	    paylike.popup({
	        title: "<?php echo $popup_title; ?>",
            currency: '<?php echo $currency_code; ?>',
	        description: "<?php echo $popup_description; ?>",
	        amount: <?php echo $amount; ?>,
            locale: '<?php echo $lc; ?>',
	        custom: {
	            orderId: '<?php echo $order_id; ?>',
	            products:  <?php echo $products; ?>,
	            customer:{
	                name: '<?php echo $name; ?>',
	                email: '<?php echo $email; ?>',
	                phoneNo: '<?php echo $telephone; ?>',
	                address: '<?php echo $address; ?>',
	                IP: '<?php echo $ip; ?>'
	                },
	            platform:{
	                name: 'opencart',
	                version: '<?php echo $VERSION; ?>',
	                },
                paylikePluginVersion: '<?php echo $plugin_version; ?>'
	            },
	    }, function(err, res) {
	        if (err)
	            return console.log(err);

	        console.log(res);
	        console.log('++++++++++++++++++++++++++++');

	        $.ajax({
	            url: 'index.php?route=extension/payment/paylike/process_payment',
	            type: 'post',
	            data: {
	                'trans_ref': res.transaction.id
	            },
	            dataType: 'json',
	            cache: false,
	            beforeSend: function() {
	                $('#button-confirm').button('loading');
	            },
	            complete: function() {
	                $('#button-confirm').button('reset');
	            },
	            success: function(json) {
		            if( json.hasOwnProperty('error') ) {
			            var html = '<div class="alert alert-danger paylikealert"><i class="fa fa-exclamation-circle"></i> Warning: ' + json.error + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
			            $('#button-confirm').closest('.buttons').before(html);
		            }
		            if( json.hasOwnProperty('redirect') ) {
			            var html = '<div class="alert alert-success paylikealert"><i class="fa fa-exclamation-circle"></i> ' + json.success + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
			            $('#button-confirm').closest('.buttons').before(html);
			            location.href = json.redirect;
		            }
	            }
	        });
	    });

	});
//--></script>