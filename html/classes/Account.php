<?php
	/* Account class used for
	 * adding/removing and linking
	 * acounts and links to customers */
	class Account {
		private $db;
		public $id;
		public $balance;
		public $type;
		public $min_balance;
		public $customers = array();

		public function __construct($db, $id) {
			if ($db && $id) {
				$this->db = $db;
				$this->id = $id;
				$this->load();
			} else {
				echo "Error! You must specify a database and the account_id.";
				exit();
			}
		}

		// Load account information using account_id
		private function load() {
			try {
				// Get and save account data to object
				$sql = "SELECT * FROM accounts JOIN account_type USING type_id WHERE account_id = $this->id;";
				$result = $this->db->query($sql);
				if ($result && $result->num_rows == 1) {
					$row = $result->fetch_assoc();
					$this->balance = $row['balance'];
					$this->type = $row['type_name'];
					$this->min_balance = $row['min_balance'];
				}

				// Get and save customer_id list
				$sql = "SELECT * FROM customer_account WHERE account_id = $this->id;";
				$result = $this->db->query($sql);
				if ($result) {
					unset($this->customers);
					$this->customers = array();
					while ($row = $result->fetch_assoc()) {
						array_push($this->customers, $row['customer_id']);
					}
				}
			} catch (Exception $e) {
				echo "There was an error loading the account: ".$e->getMessage();
				exit();
			}
		}

		// Link to a customer
		public function addCustomer($customer_id, $permission = "primary") {
			$sql = "INSERT INTO customer_account VALUES($this->id, $customer_id, '$permission');";
			$result = $this->db->query($sql);
			if ($result && $this->db->affected_rows > 0)
				return true;
			else
				return false;
		}

		// Unlink customer
		public function removeCustomer($customer_id) {
			$sql = "DELETE FROM customer_account WHERE account_id = $this->id AND customer_id = $customer_id;";
			$result = $this->db->query($sql);
			if ($result && $this->db->affected_rows > 0)
				return true;
			else
				return false;
		}

		// Remove all customer links and delete transactions
		private function unlinkAll() {
			$sql = "DELETE FROM customer_account WHERE account_id = $this->id;";
			$result = $this->db->query($sql);

			$sql2 = "DELETE FROM transactions WHERE account_id = $this->id;";
			$result2 = $this->db->query($sql2);

			if ($result && $result2)
				return true;
			else
				return false;
		}

		// Permanently remove account
		public function remove() {
			// Remove all foreign key references:
			$ready = $this->unlinkAll();
			// Then remove the account
			if ($ready) {
				$sql = "DELETE FROM accounts WHERE account_id = $this->id;";
				$result = $this->db->query($sql);
				if ($result && $this->db->affected_rows > 0)
					return true;
				else
					return false;
			} else {
				echo "Error! Failed to unlink customers or transactions.";
				exit();
			}
		}

		// Static function to add new account
		// Account::add(database, balance, account type, account name)
		public static function add($db, $balance = 0.00, $type = 1, $name) {
			$sql = "INSERT INTO accounts VALUES(NULL, $balance, $type, '$name');";
			$result = $db->query($sql);
			if ($result)
				return $db->insert_id;
			else
				return "ERROR";
		}

		// Static function to list all accounts
		// Account::listAll(database)
		public static function listAll($db) {
			$sql = "SELECT * FROM accounts;";
			$result = $db->query($sql);
			$results = array();
			while($row = $result->fetch_assoc())
				$results[] = $row;
			return $results;
		}


	}
?>