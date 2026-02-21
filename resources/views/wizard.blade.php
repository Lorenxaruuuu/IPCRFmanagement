@extends('app')

@section('header', 'Upload IPCRF')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{
    step: 1,
    totalSteps: 5,
    province: '',
    municipality: '',
    name: '',
    evaluatedFile: null,
    scannedFile: null,
    provinces: {{ json_encode($provinces) }},
    municipalities: [],

    updateMunicipalities() {
        const selected = this.provinces.find(p => p.name === this.province);
        this.municipalities = selected ? selected.municipalities : [];
        this.municipality = '';
    },

    canProceed() {
        if (this.step === 1) return this.evaluatedFile !== null;
        if (this.step === 2) return this.province && this.municipality;
        if (this.step === 3) return this.name.trim().length > 0;
        if (this.step === 4) return this.scannedFile !== null;
        return true;
    }
}">

    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-slate-500">Step <span x-text="step"></span> of <span x-text="totalSteps"></span></span>
            <span class="text-sm font-medium text-red-600" x-text="
                step === 1 ? 'Evaluated IPCRF' :
                step === 2 ? 'Location Details' :
                step === 3 ? 'Encoder Details' :
                step === 4 ? 'Scanned Copy' : 'Processing'
            "></span>
        </div>
        <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
            <div 
                class="h-full bg-red-600 transition-all duration-300"
                :style="'width: ' + ((step / totalSteps) * 100) + '%'"
            ></div>
        </div>
    </div>

    <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden min-h-[400px] flex flex-col">
        @csrf
        <div class="p-8 flex-1">
            
            <!-- STEP 1: Upload Evaluated IPCRF -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="text-center space-y-2 mb-6">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="upload" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">Upload Evaluated IPCRF</h3>
                    <p class="text-slate-500">Please upload the initial evaluated IPCRF document to begin.</p>
                </div>

                <div class="border-2 border-dashed border-slate-300 rounded-xl p-10 text-center hover:border-red-400 hover:bg-red-50 transition-colors cursor-pointer group relative">
                    <input type="file" name="evaluated_file" @change="evaluatedFile = $event.target.files[0]" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                    <i data-lucide="file-text" class="w-12 h-12 text-slate-300 group-hover:text-red-500 mx-auto mb-4 transition-colors"></i>
                    <span class="block text-sm font-medium text-slate-700 mb-1">Click to upload or drag and drop</span>
                    <span class="block text-xs text-slate-400">PDF, DOCX up to 10MB</span>
                </div>
            </div>

            <!-- STEP 2: Location -->
            <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="text-center space-y-2 mb-6">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="map-pin" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">Select Location</h3>
                    <p class="text-slate-500">Identify the origin of this IPCRF.</p>
                </div>

                <div class="space-y-4 max-w-md mx-auto">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Province</label>
                        <select name="province" x-model="province" @change="updateMunicipalities()" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all" required>
                            <option value="">Select Province</option>
                            <template x-for="p in provinces" :key="p.name">
                                <option :value="p.name" x-text="p.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Municipality</label>
                        <select name="municipality" x-model="municipality" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all disabled:bg-slate-100 disabled:text-slate-400" :disabled="!province" required>
                            <option value="">Select Municipality</option>
                            <template x-for="m in municipalities" :key="m">
                                <option :value="m" x-text="m"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Enter Name -->
            <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="text-center space-y-2 mb-6">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="user" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">Employee Details</h3>
                    <p class="text-slate-500">Enter the name of the personnel.</p>
                </div>

                <div class="max-w-md mx-auto">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" x-model="name" placeholder="Last Name, First Name, M.I." class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all" required>
                </div>
            </div>

            <!-- STEP 4: Upload Scanned IPCRF -->
            <div x-show="step === 4" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="text-center space-y-2 mb-6">
                    <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="file-text" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">Upload Scanned IPCRF</h3>
                    <p class="text-slate-500">Upload the final scanned copy with signatures.</p>
                </div>

                <div class="border-2 border-dashed border-slate-300 rounded-xl p-10 text-center hover:border-red-400 hover:bg-red-50 transition-colors cursor-pointer group relative">
                    <input type="file" name="scanned_file" @change="scannedFile = $event.target.files[0]" id="scanned_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                    <i data-lucide="file-text" class="w-12 h-12 text-slate-300 group-hover:text-red-500 mx-auto mb-4 transition-colors"></i>
                    <span class="block text-sm font-medium text-slate-700 mb-1">Click to upload scanned copy</span>
                    <span class="block text-xs text-slate-400">PDF, JPG, PNG up to 10MB</span>
                </div>
            </div>

            <!-- STEP 5: Processing (Visual Only - Form Submits on Next) -->
            <div x-show="step === 5" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="text-center space-y-8 py-8">
                    <div class="relative w-20 h-20 mx-auto">
                        <div class="absolute inset-0 border-4 border-slate-200 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-red-600 rounded-full border-t-transparent animate-spin"></div>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Processing...</h3>
                    <div class="space-y-2 text-sm text-slate-500">
                        <p class="flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4 animate-pulse"></i> Saving to Google Drive...
                        </p>
                        <p class="flex items-center justify-center gap-2">
                            <i data-lucide="bell" class="w-4 h-4 animate-pulse"></i> Notifying Admin...
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Actions -->
        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
            <button 
                type="button"
                x-show="step < 5"
                @click="step === 1 ? window.location.href='{{ route('dashboard') }}' : step--"
                class="px-6 py-2.5 text-slate-600 font-medium hover:bg-slate-200 rounded-lg transition-colors"
                x-text="step === 1 ? 'Cancel' : 'Back'"
            ></button>
            
            <button 
                type="button"
                x-show="step < 4"
                @click="if (canProceed()) step++"
                :disabled="!canProceed()"
                :class="canProceed() 
                    ? 'bg-red-600 text-white hover:bg-red-700'
                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                class="px-6 py-2.5 font-medium rounded-lg transition-colors flex items-center gap-2"
            >
                Next Step
            </button>
            <button 
                x-show="step === 4"
                @click="
                    step = 5;
                    setTimeout(() => $el.closest('form').submit(), 1000);
                "
                :disabled="step === 5"
                            class="px-6 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2 shadow-lg shadow-red-600/20"
                        >
                            Submit IPCRF
                        </button>

                    </div>
                </form>
            </div>
@endsection
