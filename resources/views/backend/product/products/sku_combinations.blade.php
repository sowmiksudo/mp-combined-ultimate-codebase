@if(count($combinations[0]) > 0)
<table class="table table-bordered aiz-table">
	<thead>
		<tr>
			<td class="text-center">
				{{translate('Variant')}}
			</td>
			<td class="text-center">
				{{translate('Variant Price')}}
			</td>
			<td class="text-center" data-breakpoints="lg">
				{{translate('SKU')}}
			</td>
            <td class="text-center" data-breakpoints="lg">
                {{ translate('Discount') }}
            </td>
            <td class="text-center" data-breakpoints="lg">
                {{ translate('Discount Type') }}
            </td>
			<td class="text-center" data-breakpoints="lg">
				{{translate('Quantity')}}
			</td>
			<td class="text-center" data-breakpoints="lg">
				{{translate('Photo')}}
			</td>
		</tr>
	</thead>
	<tbody>
	@foreach ($combinations as $key => $combination)
		@php
			$sku = '';
			foreach (explode(' ', $product_name) as $key => $value) {
				$sku .= substr($value, 0, 1);
			}

			$str = '';
			foreach ($combination as $key => $item){
				if($key > 0 ){
					$str .= '-'.str_replace(' ', '', $item);
					$sku .='-'.str_replace(' ', '', $item);
				}
				else{
					if($colors_active == 1){
						$color_name = \App\Models\Color::where('code', $item)->first()->name;
						$str .= $color_name;
						$sku .='-'.$color_name;
					}
					else{
						$str .= str_replace(' ', '', $item);
						$sku .='-'.str_replace(' ', '', $item);
					}
				}
			}
		@endphp
		@if(strlen($str) > 0)
			<tr class="variant">
				<td>
					<label for="" class="control-label">{{ $str }}</label>
				</td>
				<td>
					<input type="number" lang="en" name="price_{{ $str }}" value="" min="0" step="0.01" class="form-control" id="sku_price" required>
				</td>
				<td>
					<input type="text" name="sku_{{ $str }}" value="" class="form-control ml-3">
				</td>
                <td>
                    <div class="col-lg-12 ml-0">
                        <input type="number" lang="en" min="0" step="0.01"
                            placeholder="{{ translate('Discount') }}" name="discount_{{ $str }}" class="form-control">
                    </div>
                </td>
                <td>
                    <div class="col-lg-12 ml-0">
                        <select class="form-control aiz-selectpicker" name="discount_type_{{ $str }}">
                            <option value="amount" selected>{{ translate('Flat') }}</option>
                            <option value="percent">{{ translate('Percent') }}</option>
                        </select>
                    </div>
                </td>
				<td>
					<input type="number" lang="en" name="qty_{{ $str }}" value="10" min="0" step="1" class="form-control ml-3" required>
				</td>
				<td>
					<div class="input-group" data-toggle="aizuploader" data-type="image">
						<div class="input-group-prepend ml-3">
							<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
						</div>
						<div class="form-control file-amount text-truncate">{{ translate('Choose File') }}</div>
						<input type="hidden" name="img_{{ $str }}" class="selected-files">
					</div>
					<div class="file-preview box sm"></div>
				</td>
			</tr>
		@endif
	@endforeach
	</tbody>
</table>
@endif
