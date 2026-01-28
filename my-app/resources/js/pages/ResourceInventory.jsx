import React, { useState, useEffect } from 'react';
import {
    Plus,
    Search,
    Filter,
    Edit2,
    Trash2,
    Eye,
    Download,
    AlertCircle,
    Package,
    CheckCircle,
    Clock,
    AlertTriangle,
    Home,
    Link2,
    RotateCcw,
    ChevronLeft,
    ChevronRight,
} from 'lucide-react';
import Swal from 'sweetalert2';

// Pagination Component
function Pagination({ currentPage, totalPages, onPageChange, itemsPerPage, totalItems }) {
    const maxVisiblePages = typeof window !== 'undefined' && window.innerWidth >= 768 ? 7 : 5;
    const startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    const endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    const pages = [];
    for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
    }

    const startItem = (currentPage - 1) * itemsPerPage + 1;
    const endItem = Math.min(currentPage * itemsPerPage, totalItems);

    // Always show pagination when there are items, even if only one page
    if (totalItems === 0) return null;

    return (
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 px-4 py-3 border-t border-slate-200 bg-slate-50">
            <div className="text-sm text-slate-600">
                Showing <span className="font-medium">{startItem}</span> to <span className="font-medium">{endItem}</span> of{' '}
                <span className="font-medium">{totalItems}</span> results
            </div>
            {totalPages > 1 && (
                <div className="flex items-center gap-2">
                <button
                    onClick={() => onPageChange(currentPage - 1)}
                    disabled={currentPage === 1}
                    className="inline-flex items-center px-2 py-1.5 rounded-md border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <ChevronLeft className="w-4 h-4" />
                </button>
                {startPage > 1 && (
                    <>
                        <button
                            onClick={() => onPageChange(1)}
                            className="px-3 py-1.5 rounded-md border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                        >
                            1
                        </button>
                        {startPage > 2 && <span className="text-slate-400">...</span>}
                    </>
                )}
                {pages.map((page) => (
                    <button
                        key={page}
                        onClick={() => onPageChange(page)}
                        className={`px-3 py-1.5 rounded-md border text-sm font-medium transition-colors ${
                            page === currentPage
                                ? 'bg-emerald-600 text-white border-emerald-600'
                                : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50'
                        }`}
                    >
                        {page}
                    </button>
                ))}
                {endPage < totalPages && (
                    <>
                        {endPage < totalPages - 1 && <span className="text-slate-400">...</span>}
                        <button
                            onClick={() => onPageChange(totalPages)}
                            className="px-3 py-1.5 rounded-md border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                        >
                            {totalPages}
                        </button>
                    </>
                )}
                <button
                    onClick={() => onPageChange(currentPage + 1)}
                    disabled={currentPage === totalPages}
                    className="inline-flex items-center px-2 py-1.5 rounded-md border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <ChevronRight className="w-4 h-4" />
                </button>
            </div>
            )}
        </div>
    );
}

export function ResourceInventory() {
    const [activeTab, setActiveTab] = useState('dashboard');
    const [searchQuery, setSearchQuery] = useState('');
    const [showFilters, setShowFilters] = useState(false);
    const [resourceTypeFilter, setResourceTypeFilter] = useState('all');
    const [statusFilter, setStatusFilter] = useState('all');
    const [conditionFilter, setConditionFilter] = useState('all');
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 10; // Fixed to 10 items per page
    const filterRef = React.useRef(null);
    const [resources, setResources] = useState([]);
    const [loading, setLoading] = useState(true);
    const [events, setEvents] = useState([]);
    const [completedEventsWithResources, setCompletedEventsWithResources] = useState([]);
    const [selectedResourceHistory, setSelectedResourceHistory] = useState(null);

    const resourceTypes = ['PPE', 'Fire Equipment', 'Medical', 'Communication', 'Vehicles', 'Tools', 'Other'];
    const statusOptions = ['Available', 'In Use', 'Under Maintenance', 'Damaged', 'Missing', 'Reserved'];
    const conditionOptions = ['New', 'Good', 'Needs Repair', 'Damaged'];

    // Fetch resources from API on component mount
    useEffect(() => {
        fetchResources();
        fetchEvents();
        fetchCompletedEventsWithResources();
    }, []);


    // Close filter dropdown when clicking outside
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (filterRef.current && !filterRef.current.contains(event.target)) {
                setShowFilters(false);
            }
        };

        if (showFilters) {
            document.addEventListener('mousedown', handleClickOutside);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [showFilters]);

    const fetchResources = async () => {
        try {
            setLoading(true);
            const response = await fetch('/api/resources');
            const data = await response.json();
            
            if (!response.ok) {
                console.error('API Error:', response.status, data);
                throw new Error(data.error || 'Failed to fetch resources');
            }
            
            console.log('Resources loaded:', data);
            setResources(data.resources || data);
        } catch (error) {
            console.error('Error fetching resources:', error);
            Swal.fire('Error', 'Failed to load resources: ' + error.message, 'error');
        } finally {
            setLoading(false);
        }
    };

    const fetchEvents = async () => {
        try {
            const response = await fetch('/api/simulation-events');
            if (response.ok) {
                const data = await response.json();
                setEvents(data.events || data);
            }
        } catch (error) {
            console.error('Error fetching events:', error);
        }
    };

    const fetchCompletedEventsWithResources = async () => {
        try {
            const response = await fetch('/api/simulation-events/completed-with-resources');
            if (response.ok) {
                const data = await response.json();
                setCompletedEventsWithResources(data.events || []);
            }
        } catch (error) {
            console.error('Error fetching completed events with resources:', error);
        }
    };

    const fetchResourceHistory = async (resourceId) => {
        try {
            const response = await fetch(`/api/resources/${resourceId}/history`);
            if (!response.ok) throw new Error('Failed to fetch history');
            const data = await response.json();
            setSelectedResourceHistory(data);
        } catch (error) {
            console.error('Error fetching resource history:', error);
        }
    };

    const getStatusIcon = (status) => {
        switch (status) {
            case 'Available':
                return <CheckCircle className="w-4 h-4 text-emerald-500" />;
            case 'In Use':
                return <Clock className="w-4 h-4 text-blue-500" />;
            case 'Under Maintenance':
                return <AlertCircle className="w-4 h-4 text-yellow-500" />;
            case 'Damaged':
            case 'Missing':
                return <AlertTriangle className="w-4 h-4 text-red-500" />;
            default:
                return <Package className="w-4 h-4 text-slate-500" />;
        }
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'Available':
                return 'bg-emerald-100 text-emerald-800';
            case 'In Use':
                return 'bg-blue-100 text-blue-800';
            case 'Under Maintenance':
                return 'bg-yellow-100 text-yellow-800';
            case 'Damaged':
            case 'Missing':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-slate-100 text-slate-800';
        }
    };

    const getConditionColor = (condition) => {
        switch (condition) {
            case 'New':
                return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
            case 'Good':
                return 'bg-blue-50 text-blue-700 border border-blue-200';
            case 'Needs Repair':
                return 'bg-yellow-50 text-yellow-700 border border-yellow-200';
            case 'Damaged':
                return 'bg-red-50 text-red-700 border border-red-200';
            default:
                return 'bg-slate-50 text-slate-700 border border-slate-200';
        }
    };

    // Normalize resource strings to avoid undefined errors
    const normalizedResources = resources.map((resource) => ({
        ...resource,
        _name: (resource.name || '').toLowerCase(),
        _serial: (resource.serialNumber || resource.serial_number || '').toLowerCase(),
    }));

    const filteredResources = normalizedResources.filter((resource) => {
        const query = searchQuery.toLowerCase();
        const matchesSearch =
            query === '' ||
            resource._name.includes(query) ||
            resource._serial.includes(query);
        const matchesType = resourceTypeFilter === 'all' || resource.category === resourceTypeFilter;
        
        // Handle special 'in_use_all' filter for all assigned statuses
        let matchesStatus = statusFilter === 'all' || resource.status === statusFilter;
        if (statusFilter === 'in_use_all') {
            matchesStatus = resource.status === 'In Use' || resource.status === 'Partially Assigned' || resource.status === 'Fully Assigned';
        }
        
        const matchesCondition = conditionFilter === 'all' || resource.condition === conditionFilter;

        return matchesSearch && matchesType && matchesStatus && matchesCondition;
    });

    // Pagination
    const totalPages = Math.ceil(filteredResources.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedResources = filteredResources.slice(startIndex, endIndex);

    // Reset to page 1 when filters change
    useEffect(() => {
        setCurrentPage(1);
    }, [searchQuery, resourceTypeFilter, statusFilter, conditionFilter]);

    // Suggestions for typeahead dropdown (startsWith)
    const searchSuggestions = searchQuery
        ? normalizedResources
              .filter((resource) =>
                  resource._name.startsWith(searchQuery.toLowerCase()) ||
                  resource._serial.startsWith(searchQuery.toLowerCase())
              )
              .slice(0, 6)
        : [];

    const handleAddResource = () => {
        Swal.fire({
            title: 'Add New Resource',
            html: `
                <form id="addResourceForm" class="text-left space-y-4">
                    <input type="text" id="resourceName" placeholder="Resource Name" class="w-full px-3 py-2 border border-slate-300 rounded-md" required>
                    <select id="resourceCategory" class="w-full px-3 py-2 border border-slate-300 rounded-md" required>
                        <option value="">Select Category</option>
                        ${resourceTypes.map((type) => `<option value="${type}">${type}</option>`).join('')}
                    </select>
                    <input type="number" id="resourceQuantity" placeholder="Quantity" class="w-full px-3 py-2 border border-slate-300 rounded-md" required min="1">
                    <input type="text" id="serialNumber" placeholder="Serial Number (Optional)" class="w-full px-3 py-2 border border-slate-300 rounded-md">
                    <select id="resourceCondition" class="w-full px-3 py-2 border border-slate-300 rounded-md" required>
                        <option value="">Select Condition</option>
                        ${conditionOptions.map((cond) => `<option value="${cond}">${cond}</option>`).join('')}
                    </select>
                    <input type="text" id="location" placeholder="Storage Location (e.g., Warehouse A, Shelf 3)" class="w-full px-3 py-2 border border-slate-300 rounded-md" required>
                    <textarea id="description" placeholder="Description (Optional)" class="w-full px-3 py-2 border border-slate-300 rounded-md" rows="3"></textarea>
                    <input type="url" id="imageUrl" placeholder="Image URL (Optional)" class="w-full px-3 py-2 border border-slate-300 rounded-md">
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Add Resource',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#10b981',
            preConfirm: async () => {
                const name = document.getElementById('resourceName').value;
                const category = document.getElementById('resourceCategory').value;
                const quantity = document.getElementById('resourceQuantity').value;
                const serialNumber = document.getElementById('serialNumber').value;
                const condition = document.getElementById('resourceCondition').value;
                const location = document.getElementById('location').value;
                const description = document.getElementById('description').value;
                const imageUrl = document.getElementById('imageUrl').value;

                if (!name || !category || !quantity || !condition || !location) {
                    Swal.showValidationMessage('Please fill in all required fields');
                    return false;
                }

                try {
                    const response = await fetch('/resources', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            name,
                            category,
                            quantity: parseInt(quantity),
                            serial_number: serialNumber,
                            condition,
                            location,
                            description: description || null,
                            image_url: imageUrl || null,
                        }),
                    });

                    const data = await response.json();
                    
                    if (!response.ok) {
                        // Handle duplication error
                        if (data.message && (data.message.includes('already exists') || data.message.includes('duplicate'))) {
                            let errorMsg = data.message;
                            if (data.duplicate) {
                                errorMsg += `\n\nExisting Resource:\n- Name: ${data.duplicate.name}\n- Category: ${data.duplicate.category}`;
                                if (data.duplicate.serial_number) {
                                    errorMsg += `\n- Serial: ${data.duplicate.serial_number}`;
                                }
                            }
                            Swal.showValidationMessage(errorMsg);
                            return false;
                        }
                        throw new Error(data.message || 'Failed to add resource');
                    }
                    
                    fetchResources();
                    Swal.fire('Success', 'Resource added successfully', 'success');
                } catch (error) {
                    Swal.showValidationMessage(error.message || 'An error occurred while adding the resource');
                    return false;
                }
            },
        });
    };

    const handleEditResource = (resource) => {
        Swal.fire({
            title: 'Edit Resource',
            html: `
                <form id="editResourceForm" class="text-left space-y-4">
                    <input type="text" id="resourceName" placeholder="Resource Name" value="${resource.name}" class="w-full px-3 py-2 border border-slate-300 rounded-md" required>
                    <select id="resourceCategory" class="w-full px-3 py-2 border border-slate-300 rounded-md" required>
                        ${resourceTypes.map((type) => `<option value="${type}" ${type === resource.category ? 'selected' : ''}>${type}</option>`).join('')}
                    </select>
                    <input type="number" id="resourceQuantity" placeholder="Quantity" value="${resource.quantity}" class="w-full px-3 py-2 border border-slate-300 rounded-md" required min="1">
                    <select id="resourceStatus" class="w-full px-3 py-2 border border-slate-300 rounded-md" required>
                        ${statusOptions.map((status) => `<option value="${status}" ${status === resource.status ? 'selected' : ''}>${status}</option>`).join('')}
                    </select>
                    <select id="resourceCondition" class="w-full px-3 py-2 border border-slate-300 rounded-md" required>
                        ${conditionOptions.map((cond) => `<option value="${cond}" ${cond === resource.condition ? 'selected' : ''}>${cond}</option>`).join('')}
                    </select>
                    <input type="text" id="location" placeholder="Storage Location" value="${resource.location}" class="w-full px-3 py-2 border border-slate-300 rounded-md" required>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update Resource',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3b82f6',
            preConfirm: async () => {
                const name = document.getElementById('resourceName').value;
                const category = document.getElementById('resourceCategory').value;
                const quantity = document.getElementById('resourceQuantity').value;
                const status = document.getElementById('resourceStatus').value;
                const condition = document.getElementById('resourceCondition').value;
                const location = document.getElementById('location').value;

                if (!name || !category || !quantity || !status || !condition || !location) {
                    Swal.showValidationMessage('Please fill in all required fields');
                    return false;
                }

                try {
                    const response = await fetch(`/resources/${resource.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            _method: 'PUT',
                            name,
                            category,
                            quantity: parseInt(quantity),
                            status,
                            condition,
                            location,
                        }),
                    });

                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to update resource');
                    }
                    
                    await fetchResources();
                    return true;
                } catch (error) {
                    console.error('Update error:', error);
                    Swal.showValidationMessage(error.message);
                    return false;
                }
            },
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Success', 'Resource updated successfully', 'success');
            }
        });
    };

    const handleViewResource = (resource) => {
        // Fetch full resource details including assignments
        fetch(`/api/resources/${resource.id}/history`)
            .then(res => res.json())
            .then(data => {
                const resourceData = data.resource || resource;
                const history = data.history || [];
                const assignments = resourceData.eventAssignments || [];
                
                Swal.fire({
                    title: resourceData.name,
                    html: `
                        <div class="text-left space-y-4 max-h-[70vh] overflow-y-auto">
                            ${resourceData.image_url ? `
                                <div class="mb-4">
                                    <img src="${resourceData.image_url}" alt="${resourceData.name}" class="w-full h-48 object-cover rounded-lg border border-slate-200">
                                </div>
                            ` : ''}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase">Resource ID</p>
                                    <p class="text-sm font-medium text-slate-900">#${resourceData.id}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase">Category</p>
                                    <p class="text-sm font-medium text-slate-900">${resourceData.category}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase">Quantity</p>
                                    <p class="text-sm font-medium text-slate-900">${resourceData.quantity} units</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase">Available</p>
                                    <p class="text-sm font-medium text-slate-900">${resourceData.available || resourceData.quantity} units</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase">Status</p>
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded ${getStatusColor(resourceData.status).split(' ').join(' ')}">
                                        ${resourceData.status}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase">Condition</p>
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded ${getConditionColor(resourceData.condition).split(' ').join(' ')}">
                                        ${resourceData.condition}
                                    </span>
                                </div>
                                ${resourceData.serial_number ? `
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase">Serial Number</p>
                                    <p class="text-sm font-medium text-slate-900">${resourceData.serial_number}</p>
                                </div>
                                ` : ''}
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase">Location</p>
                                    <p class="text-sm font-medium text-slate-900">${resourceData.location}</p>
                                </div>
                            </div>
                            ${resourceData.description ? `
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase mb-1">Description</p>
                                <p class="text-sm text-slate-700">${resourceData.description}</p>
                            </div>
                            ` : ''}
                            ${assignments.length > 0 ? `
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Assigned to Events</p>
                                <div class="space-y-2">
                                    ${assignments.map(a => `
                                        <div class="p-2 bg-slate-50 rounded border border-slate-200">
                                            <p class="text-sm font-medium text-slate-900">Event ID: ${a.event_id}</p>
                                            <p class="text-xs text-slate-600">Quantity: ${a.quantity_assigned} | Status: ${a.status}</p>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                            ` : ''}
                            ${history.length > 0 ? `
                            <div>
                                <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Maintenance History</p>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    ${history.slice(0, 5).map(log => `
                                        <div class="p-2 border-l-4 border-slate-300 bg-slate-50 rounded">
                                            <p class="text-sm font-medium text-slate-900">${log.action.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                                            <p class="text-xs text-slate-600">${log.notes || 'No notes'}</p>
                                            <p class="text-xs text-slate-500 mt-1">${new Date(log.created_at).toLocaleString()}</p>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    `,
                    width: 700,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    customClass: {
                        popup: 'text-left',
                    },
                });
            })
            .catch(error => {
                console.error('Error fetching resource details:', error);
                Swal.fire('Error', 'Failed to load resource details', 'error');
            });
    };

    const handleDeleteResource = (resource) => {
        Swal.fire({
            title: 'Delete Resource?',
            text: `Are you sure you want to delete "${resource.name}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ef4444',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/resources/${resource.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content,
                        },
                    });

                    if (!response.ok) throw new Error('Failed to delete resource');
                    
                    fetchResources();
                    Swal.fire('Deleted', 'Resource has been deleted', 'success');
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }
        });
    };

    const handleExportReport = () => {
        const csvContent = [
            ['ID', 'Name', 'Category', 'Quantity', 'Available', 'Status', 'Condition', 'Location', 'Last Updated'],
            ...filteredResources.map((r) => [
                r.id,
                r.name,
                r.category,
                r.quantity,
                r.available,
                r.status,
                r.condition,
                r.location,
                r.lastUpdated,
            ]),
        ]
            .map((row) => row.join(','))
            .join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `resource-inventory-${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
    };

    const handleAssignToEvent = (resource) => {
        if (events.length === 0) {
            Swal.fire('No Events', 'No simulation events available for assignment', 'info');
            return;
        }

        // Check if resource is already assigned to an event
        const activeAssignments = resource.eventAssignments?.filter(a => a.status === 'Active') || [];
        if (activeAssignments.length > 0) {
            Swal.fire({
                title: 'Resource Already Assigned',
                html: `
                    <p class="text-left mb-3">This resource is currently assigned to:</p>
                    <ul class="text-left list-disc list-inside mb-3">
                        ${activeAssignments.map(a => `<li>Event ID: ${a.event_id} (Quantity: ${a.quantity_assigned})</li>`).join('')}
                    </ul>
                    <p class="text-left text-sm text-slate-600">Please return the resource from existing assignments before assigning to a new event.</p>
                `,
                icon: 'warning',
                confirmButtonText: 'OK',
            });
            return;
        }

        const availableQty = resource.available || resource.quantity;
        const today = new Date().toISOString().split('T')[0];

        Swal.fire({
            title: 'Assign Resource to Event',
            html: `
                <form id="assignResourceForm" class="text-left space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Simulation Event <span class="text-rose-500">*</span></label>
                        <select id="eventId" class="w-full px-3 py-2 border border-slate-300 rounded-md" required>
                            <option value="">Select Simulation Event</option>
                            ${events.map((event) => `<option value="${event.id}">${event.title} - ${new Date(event.event_date).toLocaleDateString()}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Quantity to Assign <span class="text-rose-500">*</span></label>
                        <input type="number" id="quantity" placeholder="Quantity to assign" value="1" class="w-full px-3 py-2 border border-slate-300 rounded-md" required min="1" max="${availableQty}">
                        <p class="text-xs text-slate-500 mt-1">Available: ${availableQty} units</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Responsible Staff <span class="text-rose-500">*</span></label>
                        <select id="handlerId" class="w-full px-3 py-2 border border-slate-300 rounded-md" required>
                            <option value="">Select Resource Handler</option>
                            <option value="1">Staff Member 1</option>
                            <option value="2">Staff Member 2</option>
                            <option value="3">Staff Member 3</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Assignment Date</label>
                        <input type="date" id="assignmentDate" value="${today}" class="w-full px-3 py-2 border border-slate-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Expected Return Date</label>
                        <input type="date" id="expectedReturnDate" class="w-full px-3 py-2 border border-slate-300 rounded-md">
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Assign Resource',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#10b981',
            preConfirm: async () => {
                const eventId = document.getElementById('eventId').value;
                const quantity = parseInt(document.getElementById('quantity').value);
                const handlerId = document.getElementById('handlerId').value;
                const assignmentDate = document.getElementById('assignmentDate').value;
                const expectedReturnDate = document.getElementById('expectedReturnDate').value;

                if (!eventId || !handlerId || quantity < 1) {
                    Swal.showValidationMessage('Please fill in all required fields');
                    return false;
                }

                if (quantity > availableQty) {
                    Swal.showValidationMessage(`Cannot assign ${quantity} units. Only ${availableQty} available.`);
                    return false;
                }

                try {
                    const response = await fetch(`/resources/${resource.id}/assign-to-event`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            event_id: eventId,
                            handler_id: handlerId,
                            quantity,
                            assignment_date: assignmentDate || null,
                            expected_return_date: expectedReturnDate || null,
                        }),
                    });

                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to assign resource');
                    }
                    
                    fetchResources();
                    Swal.fire('Success', 'Resource assigned to event successfully. Status changed to "In Use".', 'success');
                } catch (error) {
                    Swal.showValidationMessage(error.message || 'An error occurred while assigning the resource');
                    return false;
                }
            },
        });
    };

    const stats = {
        total: resources.length,
        available: resources.filter((r) => r.status === 'Available').length,
        inUse: resources.filter((r) => r.status === 'In Use' || r.status === 'Partially Assigned' || r.status === 'Fully Assigned').length,
        needsRepair: resources.filter((r) => r.condition === 'Needs Repair').length,
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold text-slate-900">Resource & Equipment Inventory</h1>
                    <p className="text-slate-600 mt-1">Manage all materials, equipment, and tools for disaster training and simulation events</p>
                </div>
                <button
                    onClick={handleAddResource}
                    className="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium"
                >
                    <Plus className="w-5 h-5" />
                    Add New Resource
                </button>
            </div>

            {/* Stats Cards - Clickable Filters */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                <StatCard 
                    icon={Package} 
                    label="Total Resources" 
                    value={stats.total} 
                    color="bg-blue-50 text-blue-700" 
                    onClick={() => {
                        setStatusFilter('all');
                        setResourceTypeFilter('all');
                        setConditionFilter('all');
                    }}
                    isActive={statusFilter === 'all' && resourceTypeFilter === 'all' && conditionFilter === 'all'}
                />
                <StatCard 
                    icon={CheckCircle} 
                    label="Available" 
                    value={stats.available} 
                    color="bg-emerald-50 text-emerald-700"
                    onClick={() => setStatusFilter('Available')}
                    isActive={statusFilter === 'Available'}
                />
                <StatCard 
                    icon={Clock} 
                    label="In Use" 
                    value={stats.inUse} 
                    color="bg-indigo-50 text-indigo-700"
                    onClick={() => setStatusFilter('in_use_all')}
                    isActive={statusFilter === 'in_use_all'}
                />
                <StatCard 
                    icon={AlertTriangle} 
                    label="Needs Repair" 
                    value={stats.needsRepair} 
                    color="bg-yellow-50 text-yellow-700"
                    onClick={() => setConditionFilter('Needs Repair')}
                    isActive={conditionFilter === 'Needs Repair'}
                />
            </div>

            {/* Tabs */}
            <div className="flex gap-4 border-b border-slate-200">
                {['dashboard', 'resources', 'maintenance', 'reports'].map((tab) => (
                    <button
                        key={tab}
                        onClick={() => setActiveTab(tab)}
                        className={`px-4 py-3 font-medium transition-colors ${
                            activeTab === tab
                                ? 'text-emerald-600 border-b-2 border-emerald-600'
                                : 'text-slate-600 hover:text-slate-900'
                        }`}
                    >
                        {tab.charAt(0).toUpperCase() + tab.slice(1)}
                    </button>
                ))}
            </div>

            {/* Tab Content */}
            {activeTab === 'dashboard' && (
                <div className="space-y-6">
                    {/* Search & Filters */}
                    <div className="mb-4 flex items-center gap-3">
                        <div className="flex-1 relative">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                            <input
                                type="text"
                                placeholder="Search by resource name or ID..."
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="w-full pl-10 pr-4 py-2 rounded-md border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            />
                            {searchSuggestions.length > 0 && (
                                <div className="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-md shadow-lg z-10 max-h-60 overflow-y-auto">
                                    {searchSuggestions.map((res) => (
                                        <button
                                            key={res.id}
                                            type="button"
                                            onClick={() => setSearchQuery(res.name)}
                                            className="w-full text-left px-3 py-2 text-sm hover:bg-emerald-50"
                                        >
                                            <div className="font-medium text-slate-900">{res.name}</div>
                                            <div className="text-xs text-slate-500">{res.serialNumber || res.serial_number || 'No ID'} • {res.category}</div>
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>
                        <div className="relative" ref={filterRef}>
                            <button
                                onClick={() => setShowFilters(!showFilters)}
                                className="inline-flex items-center gap-2 px-4 py-2 rounded-md border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                            >
                                <Filter className="w-4 h-4" />
                                Filters
                            </button>
                            {showFilters && (
                                <div className="absolute right-0 top-full mt-2 w-64 bg-white rounded-lg shadow-lg border border-slate-200 p-4 z-10">
                                    <div className="space-y-3">
                                        <div>
                                            <label className="block text-xs font-semibold text-slate-600 mb-1">
                                                Resource Type
                                            </label>
                                            <select
                                                value={resourceTypeFilter}
                                                onChange={(e) => setResourceTypeFilter(e.target.value)}
                                                className="w-full rounded-md border border-slate-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                            >
                                                <option value="all">All Resource Types</option>
                                                {resourceTypes.map((type) => (
                                                    <option key={type} value={type}>
                                                        {type}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                        <div>
                                            <label className="block text-xs font-semibold text-slate-600 mb-1">
                                                Status
                                            </label>
                                            <select
                                                value={statusFilter}
                                                onChange={(e) => setStatusFilter(e.target.value)}
                                                className="w-full rounded-md border border-slate-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                            >
                                                <option value="all">All Status</option>
                                                {statusOptions.map((status) => (
                                                    <option key={status} value={status}>
                                                        {status}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                        <div>
                                            <label className="block text-xs font-semibold text-slate-600 mb-1">
                                                Condition
                                            </label>
                                            <select
                                                value={conditionFilter}
                                                onChange={(e) => setConditionFilter(e.target.value)}
                                                className="w-full rounded-md border border-slate-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                            >
                                                <option value="all">All Conditions</option>
                                                {conditionOptions.map((condition) => (
                                                    <option key={condition} value={condition}>
                                                        {condition}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                        <button
                                            onClick={() => {
                                                setResourceTypeFilter('all');
                                                setStatusFilter('all');
                                                setConditionFilter('all');
                                            }}
                                            className="w-full text-xs text-slate-600 hover:text-slate-800 underline"
                                        >
                                            Clear filters
                                        </button>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Resources Table */}
                    <div className="bg-white rounded-lg border border-slate-200">
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead className="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th className="px-6 py-3 text-left font-semibold text-slate-700">Resource</th>
                                        <th className="px-6 py-3 text-left font-semibold text-slate-700">Category</th>
                                        <th className="px-6 py-3 text-left font-semibold text-slate-700">Quantity</th>
                                        <th className="px-6 py-3 text-left font-semibold text-slate-700">Status</th>
                                        <th className="px-6 py-3 text-left font-semibold text-slate-700">Condition</th>
                                        <th className="px-6 py-3 text-left font-semibold text-slate-700">Location</th>
                                        <th className="px-6 py-3 text-left font-semibold text-slate-700">Last Updated</th>
                                        <th className="px-6 py-3 text-left font-semibold text-slate-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-200">
                                    {filteredResources.length > 0 ? (
                                        paginatedResources.map((resource) => (
                                            <tr key={resource.id} className="hover:bg-slate-50 transition-colors">
                                                <td className="px-6 py-4">
                                                    <div>
                                                        <div className="font-medium text-slate-900">{resource.name}</div>
                                                        <div className="text-xs text-slate-500">{resource.serialNumber}</div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span className="inline-block px-2 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded">
                                                        {resource.category}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="text-sm font-medium text-slate-900">
                                                        {resource.available}/{resource.quantity}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className={`inline-flex items-center gap-2 px-2 py-1 rounded-md text-xs font-medium ${getStatusColor(resource.status)}`}>
                                                        {getStatusIcon(resource.status)}
                                                        {resource.status}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span className={`inline-block px-2 py-1 text-xs font-medium rounded ${getConditionColor(resource.condition)}`}>
                                                        {resource.condition}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-sm text-slate-700">{resource.location}</td>
                                                <td className="px-6 py-4 text-sm text-slate-600">{resource.lastUpdated}</td>
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-2">
                                                        {(resource.status === 'Available' || resource.status === 'Partially Assigned') && resource.available > 0 && (
                                                            <button
                                                                onClick={() => handleAssignToEvent(resource)}
                                                                className="p-2 text-emerald-600 hover:bg-emerald-50 rounded-md transition-colors"
                                                                title="Assign to Event"
                                                            >
                                                                <Link2 className="w-4 h-4" />
                                                            </button>
                                                        )}
                                                        <button
                                                            onClick={() => {
                                                                fetchResourceHistory(resource.id);
                                                                Swal.fire({
                                                                    title: `${resource.name} - Usage History`,
                                                                    html: `<div id="historyContent" class="text-left max-h-96 overflow-y-auto"></div>`,
                                                                    width: 600,
                                                                    didOpen: () => {
                                                                        if (selectedResourceHistory?.history) {
                                                                            const historyHtml = selectedResourceHistory.history.map(log => `
                                                                                <div class="p-2 border-l-4 border-slate-300 mb-2">
                                                                                    <p class="text-sm font-medium text-slate-900">${log.action}</p>
                                                                                    <p class="text-xs text-slate-600">${log.notes || 'No notes'}</p>
                                                                                    <p class="text-xs text-slate-500">${new Date(log.created_at).toLocaleString()}</p>
                                                                                </div>
                                                                            `).join('');
                                                                            document.getElementById('historyContent').innerHTML = historyHtml;
                                                                        }
                                                                    },
                                                                    showConfirmButton: false,
                                                                    showCancelButton: true,
                                                                    cancelButtonText: 'Close',
                                                                });
                                                            }}
                                                            className="p-2 text-slate-600 hover:bg-slate-100 rounded-md transition-colors"
                                                            title="View History"
                                                        >
                                                            <Eye className="w-4 h-4" />
                                                        </button>
                                                        <button
                                                            onClick={() => handleEditResource(resource)}
                                                            className="p-2 text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                                                            title="Edit"
                                                        >
                                                            <Edit2 className="w-4 h-4" />
                                                        </button>
                                                        <button
                                                            onClick={() => handleDeleteResource(resource)}
                                                            className="p-2 text-red-600 hover:bg-red-50 rounded-md transition-colors"
                                                            title="Delete"
                                                        >
                                                            <Trash2 className="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="8" className="px-6 py-8 text-center text-slate-500">
                                                No resources found matching your filters.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                        {filteredResources.length > 0 && (
                            <Pagination
                                currentPage={currentPage}
                                totalPages={totalPages}
                                onPageChange={setCurrentPage}
                                itemsPerPage={itemsPerPage}
                                totalItems={filteredResources.length}
                            />
                        )}
                    </div>
                </div>
            )}

            {activeTab === 'resources' && (
                <div className="space-y-4">
                    <div className="bg-white p-6 rounded-lg border border-slate-200">
                        <h2 className="text-xl font-semibold text-slate-900 mb-4">Resource Assignment & Usage Tracking</h2>
                        <div className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <h3 className="font-semibold text-blue-900 mb-3">Assignment Workflow</h3>
                                    <ol className="space-y-2 text-sm text-blue-800">
                                        <li>✓ Select a resource from inventory</li>
                                        <li>✓ Click "Assign to Event"</li>
                                        <li>✓ Choose simulation event</li>
                                        <li>✓ Specify quantity needed</li>
                                        <li>✓ Assign resource handler</li>
                                        <li>✓ Status changes: Available → Reserved</li>
                                    </ol>
                                </div>

                                <div className="p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                                    <h3 className="font-semibold text-emerald-900 mb-3">During Event</h3>
                                    <ol className="space-y-2 text-sm text-emerald-800">
                                        <li>✓ Resources marked as "In Use"</li>
                                        <li>✓ Track deployed vs unused items</li>
                                        <li>✓ Flag damaged/missing resources</li>
                                        <li>✓ Log deployment notes</li>
                                        <li>✓ Update condition status</li>
                                    </ol>
                                </div>
                            </div>

                            <div className="p-4 bg-slate-50 border border-slate-300 rounded-lg">
                                <h3 className="font-semibold text-slate-900 mb-3">Currently Assigned Resources</h3>
                                {resources.filter(r => r.status === 'Reserved' || r.status === 'In Use' || r.status === 'Partially Assigned' || r.status === 'Fully Assigned').length > 0 ? (
                                    <div className="space-y-2">
                                        {resources
                                            .filter(r => r.status === 'Reserved' || r.status === 'In Use' || r.status === 'Partially Assigned' || r.status === 'Fully Assigned')
                                            .map(r => (
                                                <div key={r.id} className="p-2 bg-white rounded border border-slate-200 text-sm flex justify-between items-center">
                                                    <div>
                                                        <strong>{r.name}</strong>
                                                        <div className="text-xs text-slate-600">{r.category}</div>
                                                    </div>
                                                    <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(r.status)}`}>
                                                        {r.status}
                                                    </span>
                                                </div>
                                            ))
                                        }
                                    </div>
                                ) : (
                                    <p className="text-slate-500 text-sm">No resources currently assigned to events</p>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="bg-white p-6 rounded-lg border border-slate-200">
                        <h2 className="text-lg font-semibold text-slate-900 mb-4">Post-Event Resource Return</h2>
                        <p className="text-sm text-slate-600 mb-4">
                            Resources from completed simulation events that need to be returned to inventory.
                        </p>
                        <div className="space-y-4">
                            {completedEventsWithResources.length > 0 ? (
                                completedEventsWithResources.map(event => (
                                    <div key={event.id} className="border border-slate-200 rounded-lg overflow-hidden">
                                        <div className="bg-indigo-50 px-4 py-3 border-b border-slate-200">
                                            <div className="flex items-center justify-between">
                                                <div>
                                                    <h3 className="font-medium text-indigo-900">{event.title}</h3>
                                                    <p className="text-xs text-indigo-700">
                                                        Event Date: {new Date(event.event_date).toLocaleDateString()}
                                                        {event.completed_at && ` • Completed: ${new Date(event.completed_at).toLocaleDateString()}`}
                                                    </p>
                                                </div>
                                                <span className="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded">
                                                    {event.resources.length} resource(s)
                                                </span>
                                            </div>
                                        </div>
                                        <div className="p-4 space-y-2">
                                            {event.resources.map((r, idx) => (
                                                <div key={`${event.id}-${r.id}-${idx}`} className="flex items-center justify-between p-3 bg-slate-50 rounded border border-slate-200">
                                                    <div className="flex-1">
                                                        <div className="flex items-center gap-2">
                                                            <p className="font-medium text-slate-900">{r.name}</p>
                                                            <span className="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">
                                                                Qty: {r.quantity_assigned}
                                                            </span>
                                                        </div>
                                                        <p className="text-xs text-slate-600">{r.category} {r.serial_number && `• ${r.serial_number}`}</p>
                                                    </div>
                                                    <button
                                                        onClick={() => {
                                                            Swal.fire({
                                                                title: 'Return Resource from Event',
                                                                html: `
                                                                    <div class="text-left mb-4">
                                                                        <p class="text-sm text-slate-600">Returning <strong>${r.name}</strong> (${r.quantity_assigned} unit(s)) from <strong>${event.title}</strong></p>
                                                                    </div>
                                                                    <form class="text-left space-y-3">
                                                                        <label class="block">
                                                                            <span class="text-sm font-medium text-slate-700">Condition after use:</span>
                                                                            <select id="newCondition" class="w-full px-3 py-2 border border-slate-300 rounded-md mt-1">
                                                                                <option value="Good">Good - No issues</option>
                                                                                <option value="Needs Repair">Needs Repair - Minor damage</option>
                                                                                <option value="Damaged">Damaged - Major damage</option>
                                                                            </select>
                                                                        </label>
                                                                        <label class="block">
                                                                            <span class="text-sm font-medium text-slate-700">Remarks:</span>
                                                                            <textarea id="remarks" placeholder="Enter any damage or usage notes..." class="w-full px-3 py-2 border border-slate-300 rounded-md mt-1" rows="3"></textarea>
                                                                        </label>
                                                                    </form>
                                                                `,
                                                                showCancelButton: true,
                                                                confirmButtonText: 'Return Resource',
                                                                cancelButtonText: 'Cancel',
                                                                confirmButtonColor: '#10b981',
                                                                preConfirm: async () => {
                                                                    const newCondition = document.getElementById('newCondition').value;
                                                                    const remarks = document.getElementById('remarks').value;
                                                                    
                                                                    try {
                                                                        const response = await fetch(`/resources/${r.id}/return-from-event`, {
                                                                            method: 'POST',
                                                                            headers: {
                                                                                'Content-Type': 'application/json',
                                                                                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content,
                                                                            },
                                                                            body: JSON.stringify({
                                                                                condition: newCondition,
                                                                                damage_report: remarks,
                                                                                event_id: event.id,
                                                                                quantity: r.quantity_assigned,
                                                                            }),
                                                                        });

                                                                        if (!response.ok) throw new Error('Failed to return resource');
                                                                        
                                                                        fetchResources();
                                                                        fetchCompletedEventsWithResources();
                                                                        Swal.fire('Success', 'Resource returned successfully', 'success');
                                                                    } catch (error) {
                                                                        Swal.showValidationMessage(error.message);
                                                                        return false;
                                                                    }
                                                                },
                                                            });
                                                        }}
                                                        className="px-3 py-1 text-sm bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors"
                                                    >
                                                        <RotateCcw className="w-4 h-4 inline mr-1" /> Return
                                                    </button>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <p className="text-slate-500 text-sm">No resources to return from completed events</p>
                            )}
                        </div>
                    </div>
                </div>
            )}

            {activeTab === 'maintenance' && (
                <div className="space-y-4">
                    <div className="bg-white p-6 rounded-lg border border-slate-200">
                        <h2 className="text-xl font-semibold text-slate-900 mb-4">Maintenance & Inspection Tracking</h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div className="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p className="text-sm text-yellow-700">Needs Repair</p>
                                <p className="text-2xl font-bold text-yellow-900">{resources.filter(r => r.condition === 'Needs Repair').length}</p>
                            </div>
                            <div className="p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p className="text-sm text-red-700">Under Maintenance</p>
                                <p className="text-2xl font-bold text-red-900">{resources.filter(r => r.status === 'Under Maintenance').length}</p>
                            </div>
                            <div className="p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                                <p className="text-sm text-emerald-700">Recently Maintained</p>
                                <p className="text-2xl font-bold text-emerald-900">{resources.filter(r => r.last_maintenance_date).length}</p>
                            </div>
                        </div>

                        <div className="space-y-3">
                            <h3 className="font-semibold text-slate-900">Resources Requiring Attention</h3>
                            {resources.filter(r => r.condition === 'Needs Repair' || r.status === 'Damaged').length > 0 ? (
                                <div className="space-y-2">
                                    {resources
                                        .filter(r => r.condition === 'Needs Repair' || r.status === 'Damaged')
                                        .map(r => (
                                            <div key={r.id} className="p-3 bg-slate-50 border border-red-300 rounded-lg">
                                                <div className="flex justify-between items-start mb-2">
                                                    <div>
                                                        <p className="font-medium text-slate-900">{r.name}</p>
                                                        <p className="text-xs text-slate-600">{r.serial_number || 'No serial'}</p>
                                                    </div>
                                                    <span className={`px-2 py-1 text-xs font-medium rounded ${getConditionColor(r.condition)}`}>
                                                        {r.condition}
                                                    </span>
                                                </div>
                                                <div className="flex gap-2">
                                                    <button
                                                        onClick={() => {
                                                            Swal.fire({
                                                                title: 'Schedule Maintenance',
                                                                html: `
                                                                    <form class="text-left space-y-3">
                                                                        <textarea id="notes" placeholder="Maintenance notes..." class="w-full px-3 py-2 border border-slate-300 rounded-md" rows="3"></textarea>
                                                                        <input type="text" id="technician" placeholder="Technician name (optional)" class="w-full px-3 py-2 border border-slate-300 rounded-md">
                                                                    </form>
                                                                `,
                                                                showCancelButton: true,
                                                                confirmButtonText: 'Schedule',
                                                                confirmButtonColor: '#f59e0b',
                                                                preConfirm: async () => {
                                                                    const notes = document.getElementById('notes').value;
                                                                    const technician = document.getElementById('technician').value;

                                                                    if (!notes) {
                                                                        Swal.showValidationMessage('Please add maintenance notes');
                                                                        return false;
                                                                    }

                                                                    try {
                                                                        const response = await fetch(`/resources/${r.id}/schedule-maintenance`, {
                                                                            method: 'POST',
                                                                            headers: {
                                                                                'Content-Type': 'application/json',
                                                                                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content,
                                                                            },
                                                                            body: JSON.stringify({
                                                                                notes,
                                                                                technician: technician || null,
                                                                            }),
                                                                        });

                                                                        if (!response.ok) throw new Error('Failed to schedule');
                                                                        
                                                                        fetchResources();
                                                                        Swal.fire('Success', 'Maintenance scheduled', 'success');
                                                                    } catch (error) {
                                                                        Swal.showValidationMessage(error.message);
                                                                        return false;
                                                                    }
                                                                },
                                                            });
                                                        }}
                                                        className="px-3 py-1 text-sm bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors"
                                                    >
                                                        Schedule Maintenance
                                                    </button>
                                                </div>
                                            </div>
                                        ))
                                    }
                                </div>
                            ) : (
                                <p className="text-slate-500 text-sm">No resources requiring maintenance</p>
                            )}
                        </div>
                    </div>
                </div>
            )}

            {activeTab === 'reports' && (
                <div className="space-y-4">
                    <div className="bg-white p-6 rounded-lg border border-slate-200">
                        <div className="flex items-center justify-between">
                            <div>
                                <h2 className="text-xl font-semibold text-slate-900">Resource Reports & Analytics</h2>
                                <p className="text-slate-600 mt-1">Export and view detailed resource usage statistics</p>
                            </div>
                            <button
                                onClick={handleExportReport}
                                className="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                            >
                                <Download className="w-5 h-5" />
                                Export to CSV
                            </button>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="bg-white p-6 rounded-lg border border-slate-200">
                            <h3 className="font-semibold text-slate-900 mb-4">Inventory Summary</h3>
                            <div className="space-y-3 text-sm">
                                <div className="flex justify-between">
                                    <span className="text-slate-600">Total Items:</span>
                                    <span className="font-medium text-slate-900">{resources.reduce((sum, r) => sum + r.quantity, 0)}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-slate-600">Unique Resources:</span>
                                    <span className="font-medium text-slate-900">{stats.total}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-slate-600">Damaged Items:</span>
                                    <span className="font-medium text-red-600">{resources.filter((r) => r.condition === 'Damaged').length}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-slate-600">Under Maintenance:</span>
                                    <span className="font-medium text-yellow-600">{resources.filter((r) => r.status === 'Under Maintenance').length}</span>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white p-6 rounded-lg border border-slate-200">
                            <h3 className="font-semibold text-slate-900 mb-4">Resource Distribution by Category</h3>
                            <div className="space-y-3 text-sm">
                                {resourceTypes.map((type) => {
                                    const count = resources.filter((r) => r.category === type).length;
                                    return (
                                        count > 0 && (
                                            <div key={type} className="flex justify-between items-center">
                                                <span className="text-slate-600">{type}:</span>
                                                <span className="font-medium text-slate-900">{count} items</span>
                                            </div>
                                        )
                                    );
                                })}
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Notification Alerts */}
            {stats.needsRepair > 0 && (
                <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start gap-3">
                    <AlertCircle className="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" />
                    <div>
                        <h3 className="font-medium text-yellow-900">Attention Required</h3>
                        <p className="text-sm text-yellow-800 mt-1">{stats.needsRepair} resource(s) need repair or maintenance</p>
                    </div>
                </div>
            )}
        </div>
    );
}

function StatCard({ icon: Icon, label, value, color, onClick, isActive }) {
    return (
        <div 
            className={`${color} rounded-lg p-6 border-2 transition-all cursor-pointer ${
                isActive ? 'border-emerald-500 shadow-lg scale-105' : 'border-slate-200 hover:border-slate-300 hover:shadow-md'
            }`}
            onClick={onClick}
            role="button"
            tabIndex={0}
            onKeyDown={(e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    onClick?.();
                }
            }}
        >
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-sm font-medium opacity-75">{label}</p>
                    <p className="text-3xl font-bold mt-1">{value}</p>
                </div>
                <Icon className="w-10 h-10 opacity-30" />
            </div>
        </div>
    );
}
