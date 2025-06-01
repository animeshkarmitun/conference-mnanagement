@extends('layouts.app')

@section('title', 'Travel Conflicts')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-6">Travel Conflicts</h2>
    @if(count($conflicts))
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($conflicts as $conflict)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $conflict['type'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $conflict['details'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-500">No travel conflicts detected.</p>
    @endif
</div>
@endsection 