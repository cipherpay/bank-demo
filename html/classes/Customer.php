<?php
	/* Customer class
	 * ==============
	 * used for adding/removing/updating
	 * bank customers */
	class Customer {
		private $db;
		public $id;
		public $f_name;
		public $m_name;
		public $l_name;
		public $accounts = array();

		public function __construct($db, $id) {
			if ($db && $id) {
				$this->db = $db;
				$this->id = $id;
				$this->load();
			} else {
				echo "Error! You must specify a database and the customer_id.";
				exit();
			}
		}

		// Load customer information using customer_id
		private function load() {
			try {
				// Get and save customer parameters to object
				$sql = "SELECT * FROM customers WHERE customer_id = $this->id;";
				$result = $this->db->query($sql);
				if ($result && $result->num_rows == 1) {
					$row = $result->fetch_assoc();
					$this->f_name = $row['f_name'];
					$this->m_name = $row['m_name'];
					$this->l_name = $row['l_name'];
				}

				// Get and save customer account list to object
				$sql = "SELECT account_id FROM customer_account WHERE customer_id = $this->id;";
				$result = $this->db->query($sql);
				if ($result) {
					unset($this->accounts);
					$this->accounts = array();
					while ($row = $result->fetch_assoc()) {
						array_push($this->accounts, $row['account_id']);
					}
				}
			} catch (Exception $e) {
				echo "There was an error loading the customer: ".$e->getMessage();
				exit();
			}
		}

		// Link an account
		public function addAccount($account_id, $permission = "primary") {
			$sql = "INSERT INTO customer_account VALUES($account_id, $this->id, '$permission');";
			$result = $this->db->query($sql);
			if ($result && $this->db->affected_rows > 0)
				return true;
			else
				return false;
		}

		// Unlink an account
		public function removeAccount($account_id) {
			$sql = "DELETE FROM customer_account WHERE customer_id = $this->id AND account_id = $account_id;";
			$result = $this->db->query($sql);
			if ($result && $this->db->affected_rows > 0)
				return true;
			else
				return false;
		}

		// Delete all account links
		private function unlinkAll() {
			$sql = "DELETE FROM customer_account WHERE customer_id = $this->id;";
			$result = $this->db->query($sql);
			if ($result)
				return true;
			else
				return false;
		}

		// Remove the customer from database
		public function remove () {
			// Remove all foreign key references:
			$ready = $this->unlinkAll();
			// Then remove the customer
			if ($ready) {
				$sql = "DELETE FROM customers WHERE customer_id = $this->id;";
				$result = $this->db->query($sql);
				if ($result && $this->db->affected_rows > 0)
					return true;
				else
					return false;
			} else {
				echo "Error! Failed to unlink accounts.";
				exit();
			}
		}

		// Static function to add new customer
		// Customer::add(database, first name, middle, last name)
		public static function add($db, $f_name, $m_name, $l_name) {
			$sql = "INSERT INTO customers VALUES(NULL, '$f_name', '$m_name', '$l_name');";
			$result = $db->query($sql);
			if ($result)
				return $db->insert_id;
			else
				return "ERROR";
		}

		// Static function to list all customers
		// Customer::listAll(database)
		public static function listAll($db) {
			$sql = "SELECT * FROM customers;";
			$result = $db->query($sql);
			$results = array();
			while($row = $result->fetch_assoc())
				$results[] = $row;
			return $results;
		}

	}
?>