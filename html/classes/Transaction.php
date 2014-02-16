<?php
	/* Transaction class used to
	 * process and retreive
	 * transactions */
	class Transaction {
		private $db;
		public $id;
		public $type;
		public $amount;
		public $status = "pending";
		public $statusCode = "UNKN";

		public function __construct($db, $type, $amount) {

		}

		public static function listByAcc($id, $start, $end) {

		}


	}
?>