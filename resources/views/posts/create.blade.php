@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Add a new post</h1>
        <a href="/profile/{{ auth()->user()->id }}" class="text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </a>
    </div>

    <div x-data="createPost()" class="space-y-6">
        <!-- Add a notification div at the top -->
        <div 
            x-show="notification.show" 
            x-transition
            :class="notification.type === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
            class="p-4 rounded-md mb-4"
        >
            <p x-text="notification.message"></p>
        </div>

        <!-- Image Upload -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-center">Post Image</h2>
            
            <div class="flex flex-col items-center">
                <div 
                    @click="$refs.fileInput.click()" 
                    class="cursor-pointer w-full h-96 border-2 border-dashed border-gray-300 flex items-center justify-center hover:border-gray-400 transition-colors mb-6"
                    :class="{'border-none': imageUrl}"
                >
                    <!-- Preview Image -->
                    <template x-if="imageUrl">
                        <img :src="imageUrl" class="w-full h-full object-contain">
                    </template>

                    <!-- Upload Icon and Text -->
                    <template x-if="!imageUrl">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="mt-2 block text-sm text-gray-500">Click to upload an image</span>
                        </div>
                    </template>

                    <input 
                        type="file" 
                        class="hidden" 
                        x-ref="fileInput" 
                        @change="handleImageChange" 
                        accept="image/*" 
                        name="image"
                    >
                </div>

                @error('image')
                <p class="text-red-500 text-sm italic mb-4">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Caption Form -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Caption</h2>
            
            <div class="relative">
                <textarea 
                    id="caption"
                    placeholder="Write a caption..."
                    maxlength="2200"
                    class="bg-gray-100 rounded-lg w-full py-3 px-4 leading-relaxed focus:outline-none focus:ring-2 focus:ring-primary/20 resize-none h-32 pr-16"
                    name="caption"
                    x-model="caption"
                    @input="handleCaptionChange"
                ></textarea>
                
                <div class="absolute bottom-3 right-3 text-sm text-gray-400 bg-gray-100 px-2 rounded">
                    <span x-text="captionLength + '/2200'"></span>
                </div>
            </div>

            @error('caption')
            <p class="text-red-500 text-sm italic mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4 mt-6" x-show="hasChanges">
            <a 
                href="/profile/{{ auth()->user()->id }}" 
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors"
                :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                :disabled="isSubmitting"
            >
                Cancel
            </a>
            <button 
                @click="submitPost"
                class="px-6 py-2 bg-primary text-white rounded-md hover:opacity-90 transition-opacity relative"
                :disabled="!isValid || isSubmitting"
                :class="{'opacity-50 cursor-not-allowed': !isValid || isSubmitting}"
            >
                <span x-show="!isSubmitting">Share Post</span>
                <span x-show="isSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sharing...
                </span>
            </button>
        </div>
    </div>
</div>

<script>
function createPost() {
    return {
        imageUrl: '',
        imageFile: null,
        caption: '',
        captionLength: 0,
        hasChanges: false,
        isSubmitting: false,
        notification: {
            show: false,
            message: '',
            type: 'success'
        },
        
        handleImageChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageFile = file;
                this.imageUrl = URL.createObjectURL(file);
                this.hasChanges = true;
            }
        },

        handleCaptionChange(event) {
            this.captionLength = event.target.value.length;
            this.hasChanges = this.caption !== '' || this.imageUrl !== '';
        },

        get isValid() {
            return this.imageFile && this.caption.length > 0;
        },
        
        showNotification(message, type = 'success') {
            this.notification.show = true;
            this.notification.message = message;
            this.notification.type = type;
            
            setTimeout(() => {
                this.notification.show = false;
            }, 3000);
        },
        
        async submitPost() {
            if (!this.isValid || this.isSubmitting) return;
            
            this.isSubmitting = true;

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('caption', this.caption);
            formData.append('image', this.imageFile);

            try {
                const response = await fetch('/p', {
                    method: 'POST',
                    body: formData,
                });

                if (response.ok) {
                    this.showNotification('Post created successfully!');
                    // Wait a brief moment to show the success message
                    setTimeout(() => {
                        window.location.href = '/profile/{{ auth()->user()->id }}';
                    }, 1000);
                } else {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to create post');
                }
            } catch (error) {
                this.showNotification(error.message || 'Error creating post', 'error');
                this.isSubmitting = false;
            }
        }
    }
}
</script>
@endsection


