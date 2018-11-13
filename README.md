# Z-credit Payment Gateway | Magento 2 Integration

# Description:
- Magento 2 Payment Gateway module integrate to Z-credit system | https://www.z-credit.com/
- All communication with the gateway is done using SSL encryption and no confidential cardHolder data is ever stored on the Magento 2 website. 

# Installation:
1. Copy module folder to your `magento_root_folder/app/code`
2. Go to your `magento_root_folder`. Run these commands:
    - `php ./bin/magento   setup:upgrade`
    - `php ./bin/magento   setup:di:compile`
    - `php ./bin/magento   setup:static-content:deploy`
    
    
# Configuration:
1. Go to `Stores` -> `Configuration` -> `Sales` -> `Payment Methods` -> `Z-Credit Payment Gateway`
2. Settings your `Terminal Number` and `Password` from Z-credit.
3. Settings `Installments` if it needed
3. Settings `Z-Credit Save credit card` if it needed.

# Features:
1. Pay by credit card integrate with Z-credit Payment Gateway.
2. A process called tokenization is used to run transactions with stored payment information. This lets the customers pay with a 'saved' card which are not stored on the magento 2 server at all.
2. Saved cards via Token which get from Z-credit for simple future checkouts using `Magento Vault`.
3. Supported `Instant Purchase`.
4. Supported `Intallments Payment`.
5. Returned data from server on successful transaction should be logged with the order.
6. Failed transaction info should be emailed to admin with magento `Payment Failed Email`.
7. Create/Edit orders and reorder in Admin Panel with vault token.
8. Add/Delete credit cards from customer section and adminhtml section.
9. CVV Validation on first purchase or every purchase. 

# Magento version compatible:
- Magento version  2.2.x.

# Report issues:
- For any issue please help me open an issue.

 
   
    
