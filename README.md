# CsCartBuckaroo
Buckaroo payments for CsCart

## Supported payment methods
The following payment methods are supported by this plugin:
 * iDeal
 * Credit Cards
 * Afterpay (Digiaccept)
 * Bancontact

## External libraries
This addon relies on the following Buckaroo SDK: https://github.com/Soneritics/BuckarooJSON

## Installation
 - Download the code
 - Change the folder YOUR_THEME in design/themes to your theme name
 - Upload the files to your server
 - Install the Soneritics Buckaroo addon
 - Configure the payment methods

### Styling
You will probably want to add some styling. At least to the iDeal bank list :-)
You can start with adding the following CSS to your theme's LESS file:
```css
#soneritics_buckaroo_banklist img {
  max-width: 150px;
  padding: 10px 0 10px 10px;
}
```
