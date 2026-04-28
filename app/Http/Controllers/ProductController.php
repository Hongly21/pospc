<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Required for database transactions
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventory', 'attributes'])->withCount('orderDetails');

        if ($request->has('status') && $request->status != '') {
            $query->where('Status', $request->status);
        }

        if ($request->filled('CategoryID')) {
            $query->where('CategoryID', $request->CategoryID);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'LIKE', '%' . $search . '%')
                    ->orWhere('Barcode', 'LIKE', '%' . $search . '%');
            });
        }

        $products = $query->orderBy('ProductID', 'desc')->paginate(15);
        $categories = Category::where('status', 1)->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'Name' => trim((string) $request->input('Name')),
            'Barcode' => $request->filled('Barcode') ? trim((string) $request->input('Barcode')) : null,
        ]);

        $request->validate([
            'Name' => ['required', 'string', 'max:255', Rule::unique('products', 'Name')],
            'CategoryID' => 'required|exists:categories,CategoryID',
            'CostPrice' => 'required|numeric|min:0',
            'SellPrice' => 'required|numeric|min:0',
            'StockQuantity' => 'required|integer|min:0',
            'WarrantyMonths' => 'nullable|integer|min:0',
            'Image' => 'nullable|image|max:2048',
            'Barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'Barcode')],
            'AttributesPayload' => 'nullable|string',
            'AttributeName' => 'nullable|array',
            'AttributeName.*' => 'nullable|string|max:100',
            'AttributeValue' => 'nullable|array',
            'AttributeValue.*' => 'nullable|string|max:255',
        ], [
            'Name.unique' => 'Product name already exists.',
            'Barcode.unique' => 'Barcode already exists.',
        ]);

        $validatedAttributes = $this->extractAndValidateAttributesFromRequest($request);

        try {
            // Start the transaction to ensure both Product and Inventory save safely
            DB::beginTransaction();

            $imagePath = null;
            if ($request->hasFile('Image')) {
                $file = $request->file('Image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $imagePath = $file->storeAs('products', $filename, 'public');
            }

            $product = Product::create([
                'Name' => $request->Name,
                'CategoryID' => $request->CategoryID,
                'Brand' => $request->Brand,
                'Model' => $request->Model,
                'CostPrice' => $request->CostPrice,
                'SellPrice' => $request->SellPrice,
                'WarrantyMonths' => $request->WarrantyMonths ?? 0,
                'Barcode' => $request->Barcode,
                'Description' => $request->Description,
                'Image' => $imagePath,
                'Status' => 1
            ]);

            Inventory::create([
                'ProductID' => $product->ProductID,
                'Quantity' => $request->StockQuantity,
                'ReorderLevel' => 5,
            ]);

            $attributesToInsert = $this->buildAttributeInsertData(
                (int) $product->ProductID,
                $validatedAttributes
            );

            if (!empty($attributesToInsert)) {
                ProductAttribute::insert($attributesToInsert);
            }

            // Everything succeeded, commit to the database
            DB::commit();

            return redirect()->route('products.index')->with('success', __('products.msg_created'));

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->merge([
            'Name' => trim((string) $request->input('Name')),
            'Barcode' => $request->filled('Barcode') ? trim((string) $request->input('Barcode')) : null,
        ]);

        $request->validate([
            'Name' => ['required', 'string', 'max:255', Rule::unique('products', 'Name')->ignore($id, 'ProductID')],
            'CategoryID' => 'required|exists:categories,CategoryID',
            'CostPrice' => 'required|numeric|min:0',
            'SellPrice' => 'required|numeric|min:0',
            'WarrantyMonths' => 'nullable|integer|min:0',
            'Image' => 'nullable|image|max:2048',
            //my datatype Status is not boolean it is tinyint
            // 'Status' => 'required',
            'Status' => 'required|boolean',
            'Barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'Barcode')->ignore($id, 'ProductID')],
            'AttributesPayload' => 'nullable|string',
            'AttributeName' => 'nullable|array',
            'AttributeName.*' => 'nullable|string|max:100',
            'AttributeValue' => 'nullable|array',
            'AttributeValue.*' => 'nullable|string|max:255',
        ], [
            'Name.unique' => 'Product name already exists.',
            'Barcode.unique' => 'Barcode already exists.',
        ]);

        $validatedAttributes = $this->extractAndValidateAttributesFromRequest($request);
        $data = $request->except(['Image', 'StockQuantity', 'AttributesPayload', 'AttributeName', 'AttributeValue']);

        try {
            DB::beginTransaction();

            if ($request->hasFile('Image')) {
                if ($product->Image && Storage::disk('public')->exists($product->Image)) {
                    Storage::disk('public')->delete($product->Image);
                }

                $file = $request->file('Image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $data['Image'] = $file->storeAs('products', $filename, 'public');
            }

            $product->update($data);

            $product->attributes()->delete();

            $attributesToInsert = $this->buildAttributeInsertData(
                (int) $product->ProductID,
                $validatedAttributes
            );

            if (!empty($attributesToInsert)) {
                ProductAttribute::insert($attributesToInsert);
            }

            DB::commit();

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }

        return redirect()->back()->with('success', __('products.msg_updated'));
    }

    private function extractAndValidateAttributesFromRequest(Request $request): array
    {
        $rawAttributes = [];
        $payload = $request->input('AttributesPayload');

        if (is_string($payload) && $payload !== '') {
            $decoded = json_decode($payload, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                foreach ($decoded as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $rawAttributes[] = [
                        'name' => trim((string) ($item['name'] ?? '')),
                        'value' => trim((string) ($item['value'] ?? '')),
                    ];
                }
            }
        }

        if (empty($rawAttributes)) {
            $attributeNames = $request->input('AttributeName', []);
            $attributeValues = $request->input('AttributeValue', []);

            if (!is_array($attributeNames)) {
                $attributeNames = [$attributeNames];
            }
            if (!is_array($attributeValues)) {
                $attributeValues = [$attributeValues];
            }

            foreach ($attributeNames as $index => $attributeName) {
                $rawAttributes[] = [
                    'name' => trim((string) $attributeName),
                    'value' => trim((string) ($attributeValues[$index] ?? '')),
                ];
            }
        }

        $attributes = [];
        $normalizedNames = [];
        $errors = [];

        foreach ($rawAttributes as $index => $attribute) {
            $name = $attribute['name'] ?? '';
            $value = $attribute['value'] ?? '';
            $rowNumber = $index + 1;

            if ($name === '' && $value === '') {
                continue;
            }

            if ($name === '' || $value === '') {
                $errors["AttributeName.$index"] = "Attribute row {$rowNumber} must include both name and value.";
                continue;
            }

            $normalizedName = mb_strtolower($name);
            if (isset($normalizedNames[$normalizedName])) {
                $errors["AttributeName.$index"] = "Duplicate attribute name '{$name}' is not allowed.";
                continue;
            }

            $normalizedNames[$normalizedName] = true;
            $attributes[] = [
                'AttributeName' => $name,
                'AttributeValue' => $value,
            ];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        return $attributes;
    }

    private function buildAttributeInsertData(int $productId, array $attributes): array
    {
        $now = now();
        $rows = [];

        foreach ($attributes as $attribute) {
            $rows[] = [
                'ProductID' => $productId,
                'AttributeName' => $attribute['AttributeName'],
                'AttributeValue' => $attribute['AttributeValue'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $rows;
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->orderDetails()->exists()) {
            return redirect()->back()->with('error', __('products.msg_cannot_delete'));
        }

        if ($product->Image && Storage::disk('public')->exists($product->Image)) {
            Storage::disk('public')->delete($product->Image);
        }

        $product->inventory()->delete();
        $product->delete();

        return redirect()->back()->with('success', __('products.msg_deleted'));
    }
}
