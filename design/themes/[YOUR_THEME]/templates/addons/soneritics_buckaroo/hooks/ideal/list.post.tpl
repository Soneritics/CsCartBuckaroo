<div style="padding: 0 0 20px 20px">
    <div class="clearfix">
        <div class="control-group">
            <p>{__("addons.soneritics_buckaroo.choose_bank")}</p>
            <div id="soneritics_buckaroo_banklist">
                {foreach from=fn_soneritics_buckaroo_get_ideal_issuers($cart['payment_method_data']['processor_params']['websitekey']) item=issuer key=key}
                    <input type="radio" value="{$key}" name="payment_info[ideal_issuer]" id="buckpay-{$key}">
                    <label for="buckpay-{$key}">
                        <img src="{$images_dir}/addons/soneritics_buckaroo/ideal/{$key}.png" alt="{$issuer}" title="{$issuer}">
                    </label>
                    <br/>
                {/foreach}
            </div>
        </div>
    </div>
</div>
