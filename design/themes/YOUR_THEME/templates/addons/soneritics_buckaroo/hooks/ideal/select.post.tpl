<div id="soneritics_buckaroo_ideal_select">
    <div class="clearfix">
        <div class="control-group">
            <label class="ty-control-group__title cm-profile-field cm-required ">{__("addons.soneritics_buckaroo.choose_bank")}</label>
            <select id="soneritics_buckaroo_banklist" name="payment_info[ideal_issuer]" class="">
                <option value="" id="buckpay-empty">-- {__("addons.soneritics_buckaroo.choose_bank")} --</option>
                {foreach from=fn_soneritics_buckaroo_get_ideal_issuers() item=issuer key=key}
                    <option value="{$key}" id="buckpay-{$key}">{$issuer}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>
