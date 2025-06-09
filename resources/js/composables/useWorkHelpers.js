export function useWorkHelpers() {
    const formatDate = (date) => {
        if (!date) return null;
        return new Date(date).toLocaleDateString("id-ID", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    };

    const getPriorityClass = (priority) => {
        const classes = {
            LOW: "bg-green-50 text-green-700 ring-green-600/20",
            MEDIUM: "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
            HIGH: "bg-orange-50 text-orange-700 ring-orange-600/20",
            URGENT: "bg-red-50 text-red-700 ring-red-600/20",
        };
        return classes[priority] || "bg-gray-50 text-gray-600 ring-gray-500/10";
    };

    const getStatusClass = (status) => {
        const classes = {
            "Not started": "bg-gray-50 text-gray-600 ring-gray-500/10",
            "In Progress": "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
            Completed: "bg-green-50 text-green-700 ring-green-600/20",
            "On Hold": "bg-orange-50 text-orange-700 ring-orange-600/20",
        };
        return classes[status] || "bg-gray-50 text-gray-600 ring-gray-500/10";
    };

    const getPhaseClass = (phase) => {
        const classes = {
            "Not started": "bg-gray-50 text-gray-600 ring-gray-500/10",
            Initiating: "bg-blue-50 text-blue-700 ring-blue-600/20",
            Planning: "bg-indigo-50 text-indigo-700 ring-indigo-600/20",
            Executing: "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
            Monitoring: "bg-purple-50 text-purple-700 ring-purple-600/20",
            Closing: "bg-orange-50 text-orange-700 ring-orange-600/20",
            Completed: "bg-green-50 text-green-700 ring-green-600/20",
        };
        return classes[phase] || "bg-gray-50 text-gray-600 ring-gray-500/10";
    };

    const getVerificationClass = (status) => {
        const classes = {
            "Belum Verifikasi": "bg-gray-50 text-gray-600 ring-gray-500/10",
            "Sedang Diverifikasi": "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
            "Sudah Diverifikasi": "bg-green-50 text-green-700 ring-green-600/20",
            Ditolak: "bg-red-50 text-red-700 ring-red-600/20",
        };
        return classes[status] || "bg-gray-50 text-gray-600 ring-gray-500/10";
    };

    return {
        formatDate,
        getPriorityClass,
        getStatusClass,
        getPhaseClass,
        getVerificationClass,
    };
}