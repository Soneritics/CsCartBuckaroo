<div style="padding: 0 0 20px 20px">
    <div class="clearfix">
        <div class="control-group">
            <p>{__("addons.soneritics_buckaroo.choose_bank")}</p>
            <div>
                {foreach from=fn_soneritics_buckaroo_get_ideal_issuers() item=issuer key=key}
                    <input type="radio" value="{$key}" name="payment_info[ideal_issuer]" id="buckpay-{$key}">
                    <label for="buckpay-{$key}">
                        <img src="{'app/addons/soneritics_buckaroo/img/ideal/'|cat:"$key.png"}" style="height:20px;">
                        {$issuer}
                    </label>
                    <br/>
                {/foreach}
            </div>
        </div>
    </div>
</div>
