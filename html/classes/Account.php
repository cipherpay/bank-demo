<?php
	/* Account class used for
	 * adding/removing and linking
	 * acounts and links to customers */
	class Account {
		private $db;
		public $id;
		public $balance;
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
				$sql = "SELECT * FROM accounts WHERE account_id = $this->id;";
				$result = $this->db->query($sql);
				if ($result && $result->num_rows == 1) {
					$row = $result->fetch_assoc();
					$this->balance = $row['balance'];
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

		}

		// Unlink customer
		public function removeCustomer($customer_id) {

		}

		// Remove all customer links and delete transactions
		private function unlinkAll() {

		}

		// Permanently remove account
		public function remove() {

		}

		// Static function to add new account
		// Account::add(database, customer_id, balance, permission)
		public static function add($db, $customer_id, $balance = 0.00, $permission = "primary") {

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