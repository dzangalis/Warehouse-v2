<?php

require 'vendor/autoload.php';

use productManagement\Processes;
use productManagement\Products;
use records\Logger;
use users\User;
use users\UserController;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

$productControllers = new Processes();
$authenticate = new UserController();
$logger = new Logger();

$authenticate->implementUser(new User("123", "Yogi Bear"));
$authenticate->implementUser(new User("456", "Scooby-Doo"));
$authenticate->implementUser(new User("789", "Garfield"));

$inputAccessCode = trim(readline("Enter your access code: \n"));
/** @var User $user */
$user = $authenticate->login($inputAccessCode);

if ($user == null) {
    echo "Access denied!" . PHP_EOL;
    exit();
}

echo "Welcome " . $user->getName() . PHP_EOL;

while (true) {
    $productControllers->showCommands();
    $input = strtolower(readline("Please enter a command: \n"));

    switch ($input) {
        case "1":
        case "create":

            $productName = readline("Enter product name: \n");
            $units = (int)readline("Enter amount: \n");
            $productPrice = (float)readline("Enter price: \n");
            $weeksToExpire = (int)readline("Enter the number of weeks until expiration: \n");

            $expirationDate = Carbon::now()->addWeeks($weeksToExpire);

            $product = new Products(
                Uuid::uuid4()->toString(),
                $productName,
                $productPrice,
                $units,
                Carbon::now()->format('Y-m-d H:i:s'),
                Carbon::now()->format('Y-m-d H:i:s'),
                $expirationDate->format('Y-m-d H:i:s')
            );

            $productControllers->addProduct($product);

            $logger->log(
                "Added an item with {$product->units()} units of '{$product->productName()}' costing {$product->productPrice()} each.",
                $user
            );
            break;

        case "2":
        case "list":

            echo "Listing all products:\n";
            $productControllers->displayProducts();
            break;

        case "3":
        case "update":

            $productControllers->displayProducts();
            $productId = readline("Enter the ID of the item you'd like to update: \n");
            $product = $productControllers->getProductById($productId);

            if ($product === null) {
                echo "Item with ID $productId not found.\n";
                break;
            }

            $units = (int)readline("Enter items new amount of units: ");
            $productPrice = (float)readline("Enter items new price: ");

            $productControllers->updateProduct($productId, [
                'units' => $units,
                'productPrice' => $productPrice,
                'updateTime' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            $logger->log("Updated item with the Id $productId, changing its price to: $productPrice and units to: $units", $user);
            break;

        case "4":
        case "withdraw":

            $productControllers->displayProducts();

            $productId = readline("Enter items ID: \n");
            $withdrawAmount = (int)readline("Enter the amount to withdraw: \n");
            $product = $productControllers->getProductById($productId);

            if ($product === null) {
                echo "Item with ID $productId not found.\n";
                break;
            }

            $productControllers->withdrawUnits($productId, $withdrawAmount);
            echo "Withdraw $withdrawAmount units from product ID $productId." . PHP_EOL;
            $logger->log("Updated item with Id $productId, withdrawing $withdrawAmount units from the item.", $user);
            break;

        case "5":
        case "report":

            $totalAmount = $productControllers->fullReport();
            echo "Total value of all products: $totalAmount" . PHP_EOL;

            $logger->log("Report made about the full list of products, totaling $totalAmount cost for all products in total.", $user);
            break;

        case "6":
        case "delete":

            $productControllers->displayProducts();

            $productId = readline("Enter the ID of the item you'd like to remove: \n");
            $product = $productControllers->getProductById($productId);

            if ($product === null) {
                echo "Item with ID $productId not found." . PHP_EOL;
                break;
            }

            $productControllers->delete($productId);
            $logger->log("Item with ID $productId deleted successfully.", $user);
            break;

        case "7":
        case "exit":

            echo "Have a nice day!" . PHP_EOL;
            exit;

        default:

            echo "You're command was not precise, please enter a proper command!" . PHP_EOL;
    }
}