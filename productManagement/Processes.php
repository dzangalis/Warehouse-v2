<?php

namespace productManagement;


use Carbon\Carbon;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class Processes
{

    private string $productsFile = __DIR__ . '/../records/products.json';
    private array $products = [];
    private const STORAGE_PATH = "records/";


    public function __construct()
    {
        $this->products = [];
        if (file_exists($this->productsFile)) {
            $jsonContent = file_get_contents($this->productsFile);
            $this->products = json_decode($jsonContent, true) ?? [];
        }
    }

    public function addProduct(Products $product): void
    {
        $this->products[$product->id()] = $product;
        $this->save();
    }

    public function getProductById(string $productId): ?array
    {
        return $this->products[$productId] ?? null;
    }

    public function updateProduct(string $productId, array $newData): void
    {
        if (isset($this->products[$productId])) {
            $product = (object)$this->products[$productId];

            foreach ($newData as $key => $value) {
                $product->{$key} = $value;
            }

            $this->products[$productId] = (array)$product;
            $this->save();
        }
    }

    public function withdrawUnits(string $productId, int $amountToDeduct): void
    {
        if (isset($this->products[$productId]) === true) {
            $product = (object)$this->products[$productId];

            if ($product->units >= $amountToDeduct) {
                $product->units -= $amountToDeduct;
            } else {
                echo "Insufficient units for product with ID {$productId}.\n";
                return;
            }

            $product->updateTime = Carbon::now()->format('Y-m-d H:i:s');
            $this->products[$productId] = (array)$product;

            $this->save();
        }
    }

    public function fullReport(): float
    {
        $totalAmount = 0.0;

        foreach ($this->products as $productId => $product) {
            $productValue = $product['productPrice'] * $product['units'];
            $totalAmount += $productValue;
        }

        return $totalAmount;
    }

    public function delete(string $id): void
    {
        if (isset($this->products[$id]) === false) {
            return;
        }
        unset($this->products[$id]);
        $this->save();
    }

    public function products(): array
    {
        return $this->products;
    }

    private function save(): void
    {
        file_put_contents(self::STORAGE_PATH . "products.json", json_encode($this->products, JSON_PRETTY_PRINT));
    }

    public function showCommands()
    {
        {
            $output = new ConsoleOutput();
            $table = new Table($output);
            $headers = [
                '<fg=red;options=bold>Command</>',
                '<fg=red;options=bold>Description</>'
            ];
            $table->setHeaders($headers);
            $table->setRows([
                ['1. Create', 'Create a new item for the user.'],
                ['2. List', 'List all items by the user.'],
                ['3. Update', 'Update the name and amount of a users item.'],
                ['4. Withdraw', 'Withdraw amount from a a users item.'],
                ['5. Report', 'Make a report of the users total list.'],
                ['6. Delete', 'Delete an item of the users choosing.'],
                ['7. Exit', 'Exit from the application']
            ]);
            $table->render();
        }
    }

    public function displayProducts()
    {
        if (empty($this->products)) {
            echo "No products available.\n";
        } else {
            $output = new ConsoleOutput();
            $table = new Table($output);
            $headers = [
                '<fg=red;options=bold>ID</>',
                '<fg=red;options=bold>Name</>',
                '<fg=red;options=bold>Price</>',
                '<fg=red;options=bold>Units</>',
                '<fg=red;options=bold>Created At</>',
                '<fg=red;options=bold>Last Updated</>',
                '<fg=red;options=bold>Expiration Date</>'
            ];
            $table->setHeaders($headers);

            foreach ($this->products as $items) {
                $table->addRow([
                    $items['id'],
                    $items['productName'],
                    $items['productPrice'],
                    $items['units'],
                    $items['creationTime'],
                    $items['updateTime'],
                    $items['expirationDate']
                ]);
            }

            $table->render();
        }
    }

}