@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Edit Profile</h1>
        <a href="/profile/{{ $user->id }}" class="text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </a>
    </div>
    
    <div x-data="profileEdit()" class="space-y-6">
        <!-- Profile Picture Form -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-center">Profile Picture</h2>
            
            <div class="flex flex-col items-center">
                <input type="file" name="image" id="image" class="hidden" accept="image/*" @change="handleImageChange">
                <div 
                    @click="$refs.fileInput.click()" 
                    class="cursor-pointer w-64 h-64 rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center hover:border-gray-400 transition-colors mb-6"
                    :class="{'border-none': imageUrl}"
                >
                    <!-- Preview Image -->
                    <template x-if="imageUrl">
                        <img :src="imageUrl" class="w-full h-full rounded-full object-cover">
                    </template>

                    <!-- Upload Icon and Text -->
                    <template x-if="!imageUrl">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="mt-2 block text-sm text-gray-500">Upload Photo</span>
                        </div>
                    </template>

                    <input type="file" class="hidden" x-ref="fileInput" @change="handleImageChange" accept="image/*" name="image">
                </div>

                @error('image')
                <p class="text-red-500 text-sm italic mb-4">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Bio Form -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Bio</h2>
            
            <div class="relative">
                <textarea 
                    id="description"
                    placeholder="Write a bio"
                    maxlength="100"
                    class="bg-gray-100 rounded-lg w-full py-3 px-4 leading-relaxed focus:outline-none focus:ring-2 focus:ring-primary/20 resize-none h-32 pr-16"
                    name="description"
                    x-model="bio"
                    @input="handleBioChange"
                >{{ old('description') ?? $user->profile->description }}</textarea>
                
                <div class="absolute bottom-3 right-3 text-sm text-gray-400 bg-gray-100 px-2 rounded">
                    <span id="description-counter" x-text="bioLength + '/100'"></span>
                </div>
            </div>

            @error('description')
            <p class="text-red-500 text-sm italic mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Main Action Buttons -->
        <div class="flex justify-end gap-4 mt-6" x-show="hasChanges">
            <a 
                href="/profile/{{ $user->id }}" 
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors"
            >
                Cancel
            </a>
            <button 
                @click="saveProfile"
                class="px-6 py-2 bg-primary text-white rounded-md hover:opacity-90 transition-opacity"
            >
                Save Profile
            </button>
        </div>
    </div>
</div>

<script>
function profileEdit() {
    return {
        imageUrl: '{{ $user->profile->profileImage() }}',
        originalImageUrl: '{{ $user->profile->profileImage() }}',
        bio: '{{ old('description') ?? $user->profile->description }}',
        originalBio: '{{ $user->profile->description }}',
        bioLength: {{ strlen(old('description') ?? $user->profile->description) }},
        hasChanges: false,
        imageFile: null,
        
        handleImageChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageFile = file;
                this.imageUrl = URL.createObjectURL(file);
                this.hasChanges = true;
            }
        },

        handleBioChange(event) {
            this.bioLength = event.target.value.length;
            this.hasChanges = this.bio !== this.originalBio || this.imageUrl !== this.originalImageUrl;
        },
        
        async saveProfile() {
            // Create FormData for image upload
            if (this.imageFile) {
                const imageFormData = new FormData();
                imageFormData.append('image', this.imageFile);
                imageFormData.append('_method', 'PATCH');
                
                await fetch('/profile/{{ $user->id }}/picture', {
                    method: 'POST',
                    body: imageFormData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            }

            // Update bio if changed
            if (this.bio !== this.originalBio) {
                await fetch('/profile/{{ $user->id }}/bio', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'PATCH'
                    },
                    body: JSON.stringify({
                        description: this.bio,
                        _method: 'PATCH'
                    })
                });
            }

            // Redirect to profile page
            window.location.href = '/profile/{{ $user->id }}';
        }
    }
}
</script>
@endsection
