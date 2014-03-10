bank-demo
=========

A Virtual Bank Web Application For Financial Systems Demonstrations

Written in **PHP**

## Folder Organization
### Contents of folders in this project

* ```/html``` - Contains all PHP, HTML, JavaScript and CSS code for the web application
* ```/html/api``` - Holds the PHP RESTful API code
* ```/html/classes``` - PHP classes for Customer, Account, and Transaction operations
* ```/html/css``` - All the CSS is in here
* ```/html/fonts``` - Contains glyphicons fonts used in website
* ```/html/img``` - Holds all webpage images
* ```/html/js``` - All the JavaScript is in here
* ```/sql``` - Contains all the MySQL code and database diagrams

## RESTful APIs

### Customer API

### Account API

### Transaction API

The base URL for transaction API calls is
```
/api/trans/
```

The following methods are avaible through the transaction API
* **credit** - Performs a transaction that credits an account balance
* **debit** - Performs a transaction that debits or subtracts from an account balance
* **transfer** - Transfers a specified amount between accounts
* **get** - Lists a transaction's details
* **list** - Lists all transactions for an account

#### Credit

API Format
```
method = POST
/api/trans/credit
```

API Parameters
```javascript
'amount' = The amount in decimal USD to be credited (e.g. 12.99)
'institution' = The name of the party responsible for providing funds (e.g. "Employer, Inc.")
'account_id' = The account_id of the account to be credited
```

API Response (**JSON**)
```json
# success
{
  "status" : "SUCCESS",
  "transaction_id": 0123456789
}

# error
{
  "status": "ERROR",
  "message": "Relevant error messsage"
}
```

