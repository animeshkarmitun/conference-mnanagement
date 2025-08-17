@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $idCard->name }}</h2>
                        <p class="text-gray-600">ID Card Template Details</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('id-cards.edit', $idCard) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Template
                        </a>
                        <a href="{{ route('id-cards.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Templates
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Template Information -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Template Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Type:</span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $idCard->type === 'participant' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $idCard->type)) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Conference:</span>
                                    <span class="text-gray-600">{{ $idCard->conference ? $idCard->conference->name : 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Status:</span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $idCard->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $idCard->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Created:</span>
                                    <span class="text-gray-600">{{ $idCard->created_at->format('M j, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-700">Last Updated:</span>
                                    <span class="text-gray-600">{{ $idCard->updated_at->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Color Scheme</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-700">Background:</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded border" style="background-color: {{ $idCard->background_color }}"></div>
                                        <span class="text-sm text-gray-600">{{ $idCard->background_color }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-700">Text:</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded border" style="background-color: {{ $idCard->text_color }}"></div>
                                        <span class="text-sm text-gray-600">{{ $idCard->text_color }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-700">Accent:</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded border" style="background-color: {{ $idCard->accent_color }}"></div>
                                        <span class="text-sm text-gray-600">{{ $idCard->accent_color }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Features</h3>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 {{ $idCard->include_photo ? 'text-green-500' : 'text-red-500' }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">Include Photo</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 {{ $idCard->include_qr_code ? 'text-green-500' : 'text-red-500' }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">Include QR Code</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Preview -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Card Preview</h3>
                            <div class="flex justify-center">
                                <div class="id-card-preview w-80 h-48 bg-white border-2 border-gray-300 rounded-lg shadow-lg relative overflow-hidden">
                                    <div class="absolute inset-0" style="background: linear-gradient(135deg, {{ $idCard->background_color }} 0%, {{ $idCard->accent_color }}20 100%);"></div>
                                    <div class="relative p-4 h-full flex flex-col justify-between">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="text-lg font-bold" style="color: {{ $idCard->text_color }}">John Doe</h4>
                                                <p class="text-sm" style="color: {{ $idCard->text_color }}">Speaker</p>
                                                <p class="text-xs" style="color: {{ $idCard->text_color }}">Tech Conference 2025</p>
                                            </div>
                                            @if($idCard->include_photo)
                                                <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center">
                                                    <span class="text-gray-500 text-xs">Photo</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex justify-between items-end">
                                            @if($idCard->include_qr_code)
                                                <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                                    <span class="text-gray-500 text-xs">QR</span>
                                                </div>
                                            @endif
                                            <div class="text-right">
                                                <p class="text-xs" style="color: {{ $idCard->text_color }}">ID: 12345</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="absolute top-2 right-2" style="background: {{ $idCard->accent_color }}; color: white; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; text-transform: uppercase;">
                                        {{ ucfirst($idCard->type) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                @if($idCard->isParticipantCard())
                                    <a href="#" class="block w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                        Generate Cards for Conference
                                    </a>
                                @else
                                    <a href="#" class="block w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                                        Generate Company Worker Cards
                                    </a>
                                @endif
                                
                                <form action="{{ route('id-cards.toggle-status', $idCard) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                        {{ $idCard->is_active ? 'Deactivate' : 'Activate' }} Template
                                    </button>
                                </form>
                                
                                <form action="{{ route('id-cards.destroy', $idCard) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this template?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Delete Template
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
