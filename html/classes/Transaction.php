<?php
	/* Transaction class used to
	 * process and retreive
	 * transactions */
	class Transaction {
		private $db;
		public $id;
		public $type;
		public $amount;
		public $balance_begin;
		public $balance_end;
		public $institution;
		private $status = "start";
		public $status_info = "pending";

		public function __construct($db, $type, $amount, $institution, $account, $account2 = NULL) {
			$this->db = $db;
			$this->type = $type;
			$this->amount = $amount;
			$this->institution = $institution;

			// Begin InnoDB transaction
			$this->db->autocommit(FALSE);

			switch ($this->type) {
				case 'credit':
					if ($this->credit($account)) {
						// Commit database update
						$this->db->commit();

						// Update transaction object
						$this->id = $this->db->insert_id;
						$this->status = "commit";
						$this->status_info = "CREDIT APPLIED";
					} else {
						$error = $this->db->error;

						// Rollback database update
						$this->db->rollback();

						// Insert failed transaction into database
						$trans_fail_sql = "INSERT INTO transactions VALUES(NULL, NULL, $this->balance_begin, $this->amount, '$this->institution', 'fail', '$error', $this->balance_begin, $account);";
						$result = $this->db->query($trans_fail_sql);
						$this->db->commit(); // autocommit not enabled

						// Update transaction object
						if ($result)
							$this->id = $this->db->insert_id;
						$this->status = "fail";
						$this->status_info = $error;
					}
					break;
				case 'debit':
					if ($this->debit($account)) {
						// Commit database update
						$this->db->commit();

						// Update transaction object
						$this->id = $this->db->insert_id;
						$this->status = "commit";
						$this->status_info = "AMOUNT DEBITED";
					} else {
						// Use INSUFFICIENT FUNDS for error message if set
						if ($this->status_info == "INSUFFICIENT FUNDS")
							$error = $this->status_info;
						else
							$error = $this->db->error;
						
						// Rollback database update
						$this->db->rollback();

						// Insert failed transaction into database
						$trans_fail_sql = "INSERT INTO transactions VALUES(NULL, NULL, $this->balance_begin, ".-($this->amount).", '$this->institution', 'fail', '$error', $this->balance_begin, $account);";
						$result = $this->db->query($trans_fail_sql);
						$this->db->commit(); // autocommit not enabled

						// Update transaction object
						if ($result)
							$this->id = $this->db->insert_id;
						$this->status = "fail";
						$this->status_info = $error;
					}
					break;
				case 'transfer':
					if ($this->transfer($account, $account2)) {
						// Commit database update
						$this->db->commit();

						// Update transaction object
						$this->id = NULL;
						$this->status = "commit";
						$this->status_info = "FUNDS TRANSFERRED";
					} else {
						// Rollback database update
						$this->db->rollback();

						// Update transaction object
						$this->id = NULL;
						$this->status = "fail";
						$this->status_info = "UNABLE TO TRANSFER FUNDS";
					}
					break;
				default:
					echo "Invalid transaction type or not specified.";
			}

			// Reset autocommit
			$this->db->autocommit(TRUE);
		}

		private function credit($account) {
			// Get beginning balance
			$sql = "SELECT balance FROM accounts WHERE account_id = $account;";
			$result = $this->db->query($sql);

			if ($result) {
				$row = $result->fetch_assoc();
				$this->balance_begin = $row['balance'];
				$this->balance_end = $this->balance_begin + $this->amount;

				$credit_sql = "UPDATE accounts SET balance = $this->balance_end WHERE account_id = $this->account_id;";
				$credit_result = $this->db->query($credit_sql);
				$accounts_updated = $this->db->affected_rows();

				$trans_sql = "INSERT INTO transactions VALUES(NULL, NULL, $this->balance_begin, $this->amount, '$this->institution', 'commit', 'CREDIT APPLIED', $this->balance_end, $account);";
				$trans_result = $this->db->query($trans_sql);

				if ($credit_result && $accounts_updated == 1 && $trans_result) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		private function debit($account) {
			// Get beginning balance
			$sql = "SELECT balance, min_balance FROM accounts JOIN account_type USING (type_id) WHERE account_id = $account;";
			$result = $this->db->query($sql);

			if ($result) {
				$row = $result->fetch_assoc();
				$this->balance_begin = $row['balance'];
				$min_balance = $row['min_balance'];
				$this->balance_end = $this->balance_begin - $this->amount;

				if ($this->balance_end >= $min_balance) {
					$debit_sql = "UPDATE accounts SET balance = $this->balance_end WHERE account_id = $this->account_id;";
					$debit_result = $this->db->query($debit_sql);
					$accounts_updated = $this->db->affected_rows();

					$trans_sql = "INSERT INTO transactions VALUES(NULL, NULL, $this->balance_begin, ".-($this->amount).", '$this->institution', 'commit', 'AMOUNT DEBITED', $this->balance_end, $account);";
					$trans_result = $this->db->query($trans_sql);

					if ($debit_result && $accounts_updated == 1 && $trans_result) {
						return true;
					} else {
						return false;
					}
				} else {
					$this->status_info = "INSUFFICIENT FUNDS";
					return false;
				}
			} else {
				return false;
			}
		}

		private function transfer($issuer, $acquirer) {
			$debit = $this->debit($issuer);

			$credit = $this->credit($acquirer);

			if ($credit && $debit) {
				return true;
			} else {
				return false;
			}
		}

		public static function get($db, $transaction_id) {
			$sql = "SELECT * FROM transactions WHERE transaction_id = $transaction_id;";
			$result = $db->query($sql);
			if ($result)
				return $result->fetch_assoc();
			else
				return false;
		}

		public static function listByAcc($db, $account_id, $start, $end) {
			if ($start && $end) {
				$sql = "SELECT * FROM transactions WHERE account_id = $account_id AND datetime > $start AND datetime < $end;";
			} else if ($start) {
				$sql = "SELECT * FROM transactions WHERE account_id = $account_id AND datetime > $start;";
			} else if ($end) {
				$sql = "SELECT * FROM transactions WHERE account_id = $account_id AND datetime < $end;";
			} else {
				$sql = "SELECT * FROM transactions WHERE account_id = $account_id;";
			}

			$result = $db->query($sql);
			$results = array();
			while ($row = $result->fetch_assoc())
				$results[] = $row;
			return $results;
		}
	}
?>