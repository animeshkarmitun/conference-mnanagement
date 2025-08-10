@extends('layouts.app')

@section('title', 'Tasks')

@push('styles')
<style>
    .kanban-column {
        min-height: 500px;
        transition: all 0.3s ease;
    }
    
    .kanban-column:hover {
        background-color: #fefce8;
    }
    
    .task-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .priority-indicator {
        width: 4px;
        border-radius: 2px;
    }
    
    .priority-high { background-color: #e11d48; }
    .priority-medium { background-color: #f59e0b; }
    .priority-low { background-color: #059669; }
    
    .status-badge {
        transition: all 0.2s ease;
    }
    
    .status-badge:hover {
        transform: scale(1.05);
    }
    
    .quick-action-btn {
        transition: all 0.2s ease;
    }
    
    .quick-action-btn:hover {
        transform: scale(1.05);
    }
    
    .view-toggle-btn {
        transition: all 0.2s ease;
    }
    
    .view-toggle-btn.active {
        background-color: #f59e0b;
        color: white;
    }
    
    .drag-over {
        background-color: #fef3c7;
        border: 2px dashed #f59e0b;
    }
    
    .table-row-hover {
        transition: all 0.2s ease;
    }
    
    .table-row-hover:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .task-icon {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
        font-weight: bold;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .animate-delay-1 { animation-delay: 0.1s; }
    .animate-delay-2 { animation-delay: 0.2s; }
    .animate-delay-3 { animation-delay: 0.3s; }
    .animate-delay-4 { animation-delay: 0.4s; }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }

    /* Modern color scheme overrides */
    .modern-primary {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
    }
    
    .modern-primary:hover {
        background: linear-gradient(135deg, #5855eb, #7c3aed);
    }
    
    .modern-secondary {
        background: linear-gradient(135deg, #64748b, #475569);
        color: white;
    }
    
    .modern-secondary:hover {
        background: linear-gradient(135deg, #475569, #334155);
    }
    
    .modern-success {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
    }
    
    .modern-success:hover {
        background: linear-gradient(135deg, #047857, #065f46);
    }
    
    .modern-warning {
        background: linear-gradient(135deg, #e11d48, #be123c);
        color: white;
    }
    
    .modern-warning:hover {
        background: linear-gradient(135deg, #be123c, #9f1239);
    }
    
    .modern-info {
        background: linear-gradient(135deg, #0891b2, #0e7490);
        color: white;
    }
    
    .modern-info:hover {
        background: linear-gradient(135deg, #0e7490, #155e75);
    }
    
    .modern-admin {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white;
    }
    
    .modern-admin:hover {
        background: linear-gradient(135deg, #6d28d9, #5b21b6);
    }
    
    /* Priority colors */
    .priority-high { background-color: #e11d48; }
    .priority-medium { background-color: #f59e0b; }
    .priority-low { background-color: #059669; }
</style>
@endpush

@section('content')
<!-- Enhanced Header Section -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-slate-100 animate-fade-in-up">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="mb-4 lg:mb-0">
            <h2 class="text-3xl font-bold text-slate-800">Tasks</h2>
            <p class="text-slate-600 mt-1">Manage and track conference tasks</p>
        </div>
        
        <!-- Quick Action Buttons -->
        <div class="flex flex-wrap gap-3">
            <button class="quick-action-btn modern-info p-3 rounded-full shadow-lg transition-all duration-200" title="Import Tasks">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
            </button>
            
            <button class="quick-action-btn modern-success p-3 rounded-full shadow-lg transition-all duration-200" title="Generate Reports">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </button>
            
            <button class="quick-action-btn modern-admin p-3 rounded-full shadow-lg transition-all duration-200" title="Export Data">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </button>
            
            <button class="quick-action-btn modern-warning p-3 rounded-full shadow-lg transition-all duration-200" title="Bulk Assignment">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </button>
            
            <a href="{{ route('tasks.create') }}" class="modern-primary px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Task
            </a>
        </div>
    </div>
</div>

<!-- Table View -->
<div id="table-view" class="animate-fade-in-up animate-delay-1">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tasks as $task)
                        <tr class="table-row-hover hover:bg-yellow-50 transition-all duration-200 border-b border-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 task-icon rounded-full flex items-center justify-center mr-3 shadow-lg">
                                        <span class="text-sm font-bold">
                                            {{ substr($task->title, 0, 2) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                        @if($task->theme)
                                            <div class="text-xs text-gray-500 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                {{ $task->theme }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($task->assignedTo)
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3 shadow-lg">
                                            <span class="text-sm font-medium text-yellow-700">
                                                {{ substr($task->assignedTo->first_name ?? $task->assignedTo->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $task->assignedTo->first_name ?? $task->assignedTo->name }} {{ $task->assignedTo->last_name ?? '' }}
                                            </div>
                                            <div class="text-xs text-gray-500 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $task->assignedTo->email }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                        Unassigned
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($task->due_date)
                                    @php
                                        $dueDate = is_object($task->due_date) ? $task->due_date : \Carbon\Carbon::parse($task->due_date);
                                        $isOverdue = $dueDate->isPast() && $task->status !== 'completed';
                                    @endphp
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 {{ $isOverdue ? 'text-red-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm {{ $isOverdue ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                            {{ $dueDate->format('M d, Y') }}
                                        </span>
                                        @if($isOverdue)
                                            <span class="ml-2 text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full">Overdue</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        N/A
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm border
                                    {{ $task->priority === 'high' ? 'bg-red-100 text-red-700 border-red-200' : 
                                       ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-700 border-yellow-200' : 
                                       'bg-green-100 text-green-700 border-green-200') }}">
                                    @if($task->priority === 'high')
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    @elseif($task->priority === 'medium')
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm border
                                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-700 border-green-200' : 
                                       ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-700 border-blue-200' : 
                                       ($task->status === 'cancelled' ? 'bg-red-100 text-red-700 border-red-200' : 
                                       'bg-yellow-100 text-yellow-700 border-yellow-200')) }}">
                                    @if($task->status === 'completed')
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @elseif($task->status === 'in_progress')
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @elseif($task->status === 'cancelled')
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('tasks.show', $task) }}" 
                                       class="quick-action-btn inline-flex items-center p-2 bg-blue-100 text-blue-700 hover:bg-blue-200 hover:text-blue-800 rounded-lg transition-all duration-200 border border-blue-200 shadow-sm"
                                       title="View Task Details"
                                       aria-label="View task details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    <a href="{{ route('tasks.edit', $task) }}" 
                                       class="quick-action-btn inline-flex items-center p-2 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:text-yellow-800 rounded-lg transition-all duration-200 border border-yellow-200 shadow-sm"
                                       title="Edit Task"
                                       aria-label="Edit task">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="quick-action-btn inline-flex items-center p-2 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800 rounded-lg transition-all duration-200 border border-red-200 shadow-sm"
                                                title="Delete Task"
                                                aria-label="Delete task">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                    <p class="text-gray-500 mb-2">No tasks found.</p>
                                    <a href="{{ route('tasks.create') }}" class="text-yellow-600 hover:text-yellow-700 font-medium">Create your first task</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            @if(method_exists($tasks, 'links'))
                {{ $tasks->links() }}
            @endif
        </div>
    </div>
</div>

<!-- Kanban View -->
<div id="kanban-view" class="hidden">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Pending Column -->
        <div class="kanban-column bg-gray-50 rounded-2xl p-4 border-2 border-dashed border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Pending</h3>
                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ $tasks->where('status', 'pending')->count() }}
                </span>
            </div>
            <div class="space-y-3">
                @foreach($tasks->where('status', 'pending') as $task)
                    <div class="task-card bg-white rounded-lg p-4 shadow-md border-l-4 priority-{{ $task->priority }} animate-slide-in">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="font-medium text-gray-900 text-sm">{{ $task->title }}</h4>
                            <div class="flex space-x-1">
                                @if($task->priority === 'high')
                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        @if($task->assignedTo)
                            <div class="flex items-center mb-2">
                                <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-xs font-medium text-yellow-700">
                                        {{ substr($task->assignedTo->first_name ?? $task->assignedTo->name, 0, 1) }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-600">{{ $task->assignedTo->first_name ?? $task->assignedTo->name }}</span>
                            </div>
                        @endif
                        @if($task->due_date)
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ is_object($task->due_date) ? $task->due_date->format('M d') : \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="kanban-column bg-gray-50 rounded-2xl p-4 border-2 border-dashed border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">In Progress</h3>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ $tasks->where('status', 'in_progress')->count() }}
                </span>
            </div>
            <div class="space-y-3">
                @foreach($tasks->where('status', 'in_progress') as $task)
                    <div class="task-card bg-white rounded-lg p-4 shadow-md border-l-4 priority-{{ $task->priority }} animate-slide-in">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="font-medium text-gray-900 text-sm">{{ $task->title }}</h4>
                            <div class="flex space-x-1">
                                @if($task->priority === 'high')
                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        @if($task->assignedTo)
                            <div class="flex items-center mb-2">
                                <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-xs font-medium text-yellow-700">
                                        {{ substr($task->assignedTo->first_name ?? $task->assignedTo->name, 0, 1) }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-600">{{ $task->assignedTo->first_name ?? $task->assignedTo->name }}</span>
                            </div>
                        @endif
                        @if($task->due_date)
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ is_object($task->due_date) ? $task->due_date->format('M d') : \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Completed Column -->
        <div class="kanban-column bg-gray-50 rounded-2xl p-4 border-2 border-dashed border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Completed</h3>
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ $tasks->where('status', 'completed')->count() }}
                </span>
            </div>
            <div class="space-y-3">
                @foreach($tasks->where('status', 'completed') as $task)
                    <div class="task-card bg-white rounded-lg p-4 shadow-md border-l-4 priority-{{ $task->priority }} animate-slide-in opacity-75">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="font-medium text-gray-900 text-sm line-through">{{ $task->title }}</h4>
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        @if($task->assignedTo)
                            <div class="flex items-center mb-2">
                                <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-xs font-medium text-yellow-700">
                                        {{ substr($task->assignedTo->first_name ?? $task->assignedTo->name, 0, 1) }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-600">{{ $task->assignedTo->first_name ?? $task->assignedTo->name }}</span>
                            </div>
                        @endif
                        @if($task->due_date)
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ is_object($task->due_date) ? $task->due_date->format('M d') : \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Cancelled Column -->
        <div class="kanban-column bg-gray-50 rounded-2xl p-4 border-2 border-dashed border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Cancelled</h3>
                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ $tasks->where('status', 'cancelled')->count() }}
                </span>
            </div>
            <div class="space-y-3">
                @foreach($tasks->where('status', 'cancelled') as $task)
                    <div class="task-card bg-white rounded-lg p-4 shadow-md border-l-4 priority-{{ $task->priority }} animate-slide-in opacity-50">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="font-medium text-gray-900 text-sm line-through">{{ $task->title }}</h4>
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        @if($task->assignedTo)
                            <div class="flex items-center mb-2">
                                <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-xs font-medium text-yellow-700">
                                        {{ substr($task->assignedTo->first_name ?? $task->assignedTo->name, 0, 1) }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-600">{{ $task->assignedTo->first_name ?? $task->assignedTo->name }}</span>
                            </div>
                        @endif
                        @if($task->due_date)
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ is_object($task->due_date) ? $task->due_date->format('M d') : \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableViewBtn = document.getElementById('table-view-btn');
    const kanbanViewBtn = document.getElementById('kanban-view-btn');
    const tableView = document.getElementById('table-view');
    const kanbanView = document.getElementById('kanban-view');

    // View toggle functionality
    tableViewBtn.addEventListener('click', function() {
        tableViewBtn.classList.add('active');
        kanbanViewBtn.classList.remove('active');
        tableView.classList.remove('hidden');
        kanbanView.classList.add('hidden');
    });

    kanbanViewBtn.addEventListener('click', function() {
        kanbanViewBtn.classList.add('active');
        tableViewBtn.classList.remove('active');
        kanbanView.classList.remove('hidden');
        tableView.classList.add('hidden');
    });

    // Add hover effects to task cards
    const taskCards = document.querySelectorAll('.task-card');
    taskCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush 