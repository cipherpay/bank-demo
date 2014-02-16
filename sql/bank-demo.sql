SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `bank` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `bank` ;

-- -----------------------------------------------------
-- Table `bank`.`account_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bank`.`account_type` ;

CREATE  TABLE IF NOT EXISTS `bank`.`account_type` (
  `type_id` INT UNSIGNED NOT NULL ,
  `type_name` VARCHAR(45) NULL DEFAULT NULL ,
  `min_balance` DECIMAL(12,2) NOT NULL DEFAULT 0.00 ,
  PRIMARY KEY (`type_id`) ,
  UNIQUE INDEX `type_id_UNIQUE` (`type_id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bank`.`accounts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bank`.`accounts` ;

CREATE  TABLE IF NOT EXISTS `bank`.`accounts` (
  `account_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `balance` DECIMAL(12,2) NOT NULL ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `type_id` INT UNSIGNED NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`account_id`, `type_id`) ,
  UNIQUE INDEX `account_id_UNIQUE` (`account_id` ASC) ,
  INDEX `fk_accounts_account_type1` (`type_id` ASC) ,
  CONSTRAINT `fk_accounts_account_type1`
    FOREIGN KEY (`type_id` )
    REFERENCES `bank`.`account_type` (`type_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 10000;


-- -----------------------------------------------------
-- Table `bank`.`customers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bank`.`customers` ;

CREATE  TABLE IF NOT EXISTS `bank`.`customers` (
  `customer_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `f_name` VARCHAR(255) NOT NULL ,
  `m_name` VARCHAR(255) NULL DEFAULT NULL ,
  `l_name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`customer_id`) ,
  UNIQUE INDEX `customer_id_UNIQUE` (`customer_id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bank`.`customer_account`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bank`.`customer_account` ;

CREATE  TABLE IF NOT EXISTS `bank`.`customer_account` (
  `account_id` INT UNSIGNED NOT NULL ,
  `customer_id` INT UNSIGNED NOT NULL ,
  `privilege` ENUM('primary','admin','user') NOT NULL DEFAULT 'user' ,
  PRIMARY KEY (`account_id`, `customer_id`) ,
  INDEX `fk_customer_account_accounts` (`account_id` ASC) ,
  INDEX `fk_customer_account_customers1` (`customer_id` ASC) ,
  CONSTRAINT `fk_customer_account_accounts`
    FOREIGN KEY (`account_id` )
    REFERENCES `bank`.`accounts` (`account_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_customer_account_customers1`
    FOREIGN KEY (`customer_id` )
    REFERENCES `bank`.`customers` (`customer_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bank`.`transactions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bank`.`transactions` ;

CREATE  TABLE IF NOT EXISTS `bank`.`transactions` (
  `transaction_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `datetime` TIMESTAMP NOT NULL DEFAULT NOW() ,
  `balance_begin` DECIMAL(12,2) NULL DEFAULT NULL ,
  `amount` DECIMAL(12,2) NULL DEFAULT NULL ,
  `institution` VARCHAR(255) NULL DEFAULT NULL ,
  `status` ENUM('start','fail','commit') NOT NULL DEFAULT 'start' ,
  `status_info` VARCHAR(45) NULL DEFAULT NULL ,
  `balance_end` DECIMAL(12,2) NULL DEFAULT NULL ,
  `account_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`transaction_id`, `account_id`) ,
  UNIQUE INDEX `transaction_id_UNIQUE` (`transaction_id` ASC) ,
  INDEX `fk_transactions_accounts1` (`account_id` ASC) ,
  CONSTRAINT `fk_transactions_accounts1`
    FOREIGN KEY (`account_id` )
    REFERENCES `bank`.`accounts` (`account_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
