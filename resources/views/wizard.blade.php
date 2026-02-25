@extends('app')

@section('header', 'Upload IPCRF')

@section('content')
<div class="max-w-3xl mx-auto" 
x-data="{ 
    step: 1,
    totalSteps: 4,
    province: '{{ old('province') }}',
    municipality: '{{ old('municipality') }}',
    name: '{{ old('name') }}',
    provinces: {{ json_encode($provinces) }},
    municipalities: [],
    scannedFileName: '',
    isSubmitting: false,

    init() {
        // Restore municipalities if old input exists
        if (this.province) {
            this.updateMunicipalities();
        }
        // Restore step based on validation errors
        @if($errors->has('province') || $errors->has('municipality'))
            this.step = 1;
        @elseif($errors->has('name'))
            this.step = 2;
        @elseif($errors->has('scanned_file'))
            this.step = 3;
        @endif
    },

    updateMunicipalities() {
        const selected = this.provinces.find(p => p.name === this.province);
        this.municipalities = selected ? selected.municipalities : [];
        if (!this.municipalities.includes(this.municipality)) {
            this.municipality = '';
        }
    },

    updateScannedFileName(event) {
        this.scannedFileName = event.target.files.length > 0 ? event.target.files[0].name : '';
    },

    validateAndSubmit(event) {
        if(!this.scannedFileName) { 
            alert('Please upload the scanned IPCRF file.'); 
            return false; 
        }
        this.isSubmitting = true;
        this.step = 4;
        // Allow form to submit naturally
        return true;
    }
}" x-init="init()">

    <!-- Display Validation Errors -->
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-slate-500">
                Step <span x-text="step"></span> of <span x-text="totalSteps"></span>
            </span>
            <span class="text-sm font-medium text-red-600"
                x-text="step === 4 ? 'Processing' : (step === 3 ? 'Scanned Copy' : (step === 2 ? 'Encoder Details' : 'Location Details'))">
            </span>
        </div>

        <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
            <div 
                class="h-full bg-red-600 transition-all duration-300"
                :style="'width: ' + ((step / totalSteps) * 100) + '%'">
            </div>
        </div>
    </div>

    <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data"
        class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden min-h-[400px] flex flex-col"
        @submit="isSubmitting = true">
        @csrf

        <div class="p-8 flex-1">

            <!-- STEP 1: Location -->
            <div x-show="step === 1" x-transition>
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
                        <select name="province" x-model="province" @change="updateMunicipalities()"
                            class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('province') border-red-500 @enderror"
                            required>
                            <option value="">Select Province</option>
                            <template x-for="p in provinces" :key="p.name">
                                <option :value="p.name" x-text="p.name"></option>
                            </template>
                        </select>
                        @error('province') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Municipality</label>
                        <select name="municipality" x-model="municipality"
                            :disabled="!province"
                            class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 disabled:bg-slate-100 @error('municipality') border-red-500 @enderror"
                            required>
                            <option value="">Select Municipality</option>
                            <template x-for="m in municipalities" :key="m">
                                <option :value="m" x-text="m"></option>
                            </template>
                        </select>
                        @error('municipality') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- STEP 2: Name -->
            <div x-show="step === 2" x-cloak x-transition>
                <div class="text-center space-y-2 mb-6">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="user" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">Employee Details</h3>
                    <p class="text-slate-500">Enter the name of the personnel.</p>
                </div>

                <div class="max-w-md mx-auto">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" x-model="name"
                        placeholder="Last Name, First Name, M.I."
                        class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror"
                        required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- STEP 3: Upload Scanned -->
            <div x-show="step === 3" x-cloak x-transition>
                <div class="text-center space-y-2 mb-6">
                    <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="file-text" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">Upload Scanned IPCRF</h3>
                    <p class="text-slate-500">Upload the final scanned copy with signatures.</p>
                </div>

                <div class="border-2 border-dashed border-slate-300 rounded-xl p-10 text-center hover:border-red-400 hover:bg-red-50 transition cursor-pointer relative @error('scanned_file') border-red-500 bg-red-50 @enderror">
                    <input type="file"
                        name="scanned_file"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        @change="updateScannedFileName($event)"
                        accept=".pdf,.jpg,.jpeg,.png"
                        required>

                    <i data-lucide="file-text" class="w-12 h-12 text-slate-300 mx-auto mb-4"></i>
                    <span class="block text-sm font-medium text-slate-700 mb-1"
                        x-text="scannedFileName || 'Click to upload scanned copy'"></span>
                    <span class="block text-xs text-slate-400">PDF, JPG, PNG up to 10MB</span>
                    @error('scanned_file') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- STEP 4: Processing -->
            <div x-show="step === 4 || isSubmitting" x-cloak x-transition>
                <div class="text-center space-y-8 py-8">
                    <div class="relative w-20 h-20 mx-auto">
                        <div class="absolute inset-0 border-4 border-slate-200 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-red-600 rounded-full border-t-transparent animate-spin"></div>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Processing...</h3>
                    <p class="text-sm text-slate-500">Saving to Google Drive and notifying admin...</p>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-between items-center">

            <!-- Back -->
            <button type="button"
                x-show="step > 1 && step < 4 && !isSubmitting"
                @click="step--"
                class="px-6 py-2.5 text-slate-600 font-medium hover:bg-slate-200 rounded-lg">
                Back
            </button>

            <!-- Next -->
            <button type="button"
                x-show="step < 3"
                @click="
                    if(step === 1 && (!province || !municipality)) { alert('Please select location.'); return; }
                    if(step === 2 && !name) { alert('Please enter employee name.'); return; }
                    step++;
                "
                class="ml-auto px-6 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 flex items-center gap-2">
                Next Step
                <i data-lucide='arrow-right' class="w-4 h-4"></i>
            </button>

            <!-- Submit Button - Fixed -->
            <button type="submit" 
                x-show="step === 3 && !isSubmitting" 
                @click.prevent="if(validateAndSubmit()) { $el.closest('form').submit(); }"
                class="ml-auto px-6 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 flex items-center gap-2">
                Submit IPCRF
                <i data-lucide='arrow-right' class="w-4 h-4"></i>
            </button>
        </div>
    </form>
</div>
@endsection