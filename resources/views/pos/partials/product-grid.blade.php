@foreach ($products as $product)
    <div class="col-6 col-sm-4 col-md-4 col-lg-3 product-card">
        <div class="card h-100 border-0 shadow-sm btn-add-cart" data-id="{{ $product->ProductID }}"
            data-name="{{ $product->Name }}" data-price="{{ $product->SellPrice }}"
            data-stock="{{ $product->inventory->Quantity }}"
            data-tax-rate="{{ $product->tax ? $product->tax->Rate : $product->category->tax->Rate ?? 0 }}"
            data-attributes="{{ $product->attributes->map(fn($a) => $a->AttributeName . ': ' . $a->AttributeValue)->implode(', ') }}">
            <div class="card-body text-center p-3 p-sm-2">
                <div class="product-image-wrapper mb-2">
                    @if ($product->Image)
                        @if(str_starts_with($product->Image, 'http'))
                            <img src="{{ $product->Image }}" alt="{{ $product->Name }}"
                                class="img-fluid pos-product-img" loading="lazy">
                        @else
                            <img src="{{ asset('storage/' . $product->Image) }}" alt="{{ $product->Name }}"
                                class="img-fluid pos-product-img" loading="lazy">
                        @endif
                    @else
                        <img src="{{ asset('images/no-image.png') }}" alt="No Image"
                            class="img-fluid pos-product-img" loading="lazy">
                    @endif
                </div>
                <h6 class="card-title fw-bold">{{ $product->Name }}</h6>
                @if ($product->attributes->isNotEmpty())
                    <small class="text-muted d-block mb-1">
                        @foreach ($product->attributes->take(2) as $attribute)
                            <span>{{ $attribute->AttributeName }}:
                                {{ $attribute->AttributeValue }}</span>
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                        @if ($product->attributes->count() > 2)
                            <span>...</span>
                        @endif
                    </small>
                @endif
                <div class="text-primary">{{ __('pos.price') }}:
                    ${{ number_format($product->SellPrice, 2) }}</div>
                @if ($product->tax || $product->category->tax)
                    <small class="text-muted d-block">{{ __('pos.tax_rate') }}:
                        {{ number_format($product->tax?->Rate ?? ($product->category->tax?->Rate ?? 0), 2) }}%</small>
                @endif
                <small class="text-muted">{{ __('pos.stock') }}:
                    {{ $product->inventory->Quantity }}</small>
            </div>
        </div>
    </div>
@endforeach
