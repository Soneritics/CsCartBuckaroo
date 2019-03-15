<div id="soneritics_buckaroo_ideal_select_inline">
    <div class="clearfix">
        <div class="control-group">
            <label class="ty-control-group__title cm-profile-field cm-required span4 offset2">{__("addons.soneritics_buckaroo.choose_bank")}</label>
            <select id="soneritics_buckaroo_banklist" name="payment_info[ideal_issuer]" class="span6">
                <option value="" id="buckpay-empty">-- {__("addons.soneritics_buckaroo.choose_bank")} --</option>
                {foreach from=fn_soneritics_buckaroo_get_ideal_issuers($cart['payment_method_data']['processor_params']['websitekey']) item=issuer key=key}
                    <option value="{$key}" id="buckpay-{$key}">{$issuer}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>
