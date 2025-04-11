class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300
rounded-md">{{ old('address', $representative->address) }}</textarea>
@error('address')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>

<div class="col-span-6 sm:col-span-3">
    <label for="user_id" class="block text-sm font-medium text-gray-700">Associated User</label>
    <select name="user_id" id="user_id"
        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <option value="">Select a user</option>
        @foreach ($users as $user)
            <option value="{{ $user->id }}"
                {{ old('user_id', $representative->user_id) == $user->id ? 'selected' : '' }}>
                {{ $user->name }} ({{ $user->email }})
            </option>
        @endforeach
    </select>
    @error('user_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
</div>

<div class="mt-6 flex items-center justify-end">
    <a href="{{ route('representatives.index') }}"
        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
    <button type="submit"
        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
        Update Representative
    </button>
</div>
</form>
</div>
</div>
</div>
</div>
@endsection
