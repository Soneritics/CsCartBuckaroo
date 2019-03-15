<p>{__('addons.soneritics_buckaroo.afterpay_header')}</p>

<div class="ty-control-group">
    <label for="afterpay_date_of_birth" class="ty-control-group__title cm-required">{__('addons.soneritics_buckaroo.gender')}</label>
    <select name="payment_info[gender]">
        <option value="1"{if $cart.payment_info.gender==1} selected="selected"{/if}>{__('addons.soneritics_buckaroo.male')}</option>
        <option value="2"{if $cart.payment_info.gender==2} selected="selected"{/if}>{__('addons.soneritics_buckaroo.female')}</option>
    </select>
</div>

<div class="ty-control-group">
    <label for="afterpay_date_of_birth" class="ty-control-group__title cm-required">{__('addons.soneritics_buckaroo.dob')}</label>
    <select name="payment_info[date_of_birth_raw][day]">
        {for $day=1 to 31}
            <option value="{$day}"{if $cart.payment_info.date_of_birth_raw.day==$day} selected="selected"{/if}>{$day}</option>
        {/for}
    </select>
    -
    <select name="payment_info[date_of_birth_raw][month]">
        {for $month=1 to 12}
            <option value="{$month}"{if $cart.payment_info.date_of_birth_raw.month==$month} selected="selected"{/if}>{__("month_name_$month")}</option>
        {/for}
    </select>
    -
    <select name="payment_info[date_of_birth_raw][year]">
        {for $year=(date('Y')-90) to (date('Y')-18)}
            <option value="{$year}"{if $cart.payment_info.date_of_birth_raw.year==$year} selected="selected"{/if}>{$year}</option>
        {/for}
    </select>
</div>
{if strlen($cart.user_data.b_phone) > 0}
    <div class="ty-control-group">
        <label for="afterpay_phone_number" class="ty-control-group__title cm-required">{__('addons.soneritics_buckaroo.mobile')}</label>
        <input id="afterpay_phone_number" size="35" type="text" name="payment_info[afterpay_phone_number]" value="{str_replace('.', '', str_replace('+', '00', str_replace(' ', '', trim($cart.user_data.b_phone))))}" class="ty-input-text cm-autocomplete-off" />
    </div>
{else}
    <div class="ty-control-group">
        <label for="afterpay_phone_number" class="ty-control-group__title cm-required">{__('addons.soneritics_buckaroo.mobile')}</label>
        <input id="afterpay_phone_number" size="35" type="text" name="payment_info[afterpay_phone_number]" value="{$cart.payment_info.afterpay_phone_number}" class="ty-input-text cm-autocomplete-off" />
    </div>
{/if}
<div class="ty-control-group" style="display:none;">
    <label for="afterpay_house_number" class="ty-control-group__title cm-required">{__('addons.soneritics_buckaroo.housenr')}</label>
    <input id="afterpay_house_number" size="35" type="text" name="payment_info[afterpay_house_number]" value="{$cart.user_data.b_address_2}{if $cart.user_data.fields.40} {$cart.user_data.fields.40}{/if}" class="cm-value-integer ty-input-text cm-autocomplete-off" />
</div>

<div class="ty-control-group">
    <label for="afterpay_accept_terms" class="ty-control-group__title cm-required">{__('addons.soneritics_buckaroo.afterpay_terms_head')}</label>
    <p>{__('addons.soneritics_buckaroo.afterpay_terms_body')}</p>
    <input for="afterpay_accept_terms" type="checkbox" name="payment_info[accepted_terms]" class="ty-input-checkbox" /> {__('addons.soneritics_buckaroo.afterpay_terms_accept')}
</div>
