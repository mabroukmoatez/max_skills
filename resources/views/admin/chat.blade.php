<div>
    <div class="chat-container row">
        <div class="sidebar col-md-4">
            <div class="row m-1" style="margin-bottom:20px !important;">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h4 class="main-title">Live Chat</h4>
                </div>
            </div>
            <div class="input-group mb-3 search-wrapper">
                <span class="search-icon-q">
                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="10.1899" cy="9.76663" rx="9.06015" ry="8.98856" stroke="#041925"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M16.4915 16.4851L20.0435 20" stroke="#041925" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                <input type="text" class="search-input placeholder-black"
                    style="padding-left:50px !important;border-radius: 11px !important; background: #F5F5F5 !important;"
                    placeholder="Chercher..." wire:model.live="search" />
            </div>
            <ul class="nav nav-tabs mb-3" id="chatTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}" id="general-tab"
                        wire:click="setTab('general')" data-bs-toggle="tab" data-bs-target="#general" type="button"
                        role="tab" aria-controls="general"
                        aria-selected="{{ $activeTab === 'general' ? 'true' : 'false' }}">Général</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'non-lus' ? 'active' : '' }}" id="non-lus-tab"
                        wire:click="setTab('non-lus')" data-bs-toggle="tab" data-bs-target="#non-lus" type="button"
                        role="tab" aria-controls="non-lus"
                        aria-selected="{{ $activeTab === 'non-lus' ? 'true' : 'false' }}">Non Lus <span
                            class="badge">{{ $unreadMessagesCount }}</span></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'ouvert' ? 'active' : '' }}" id="ouvert-tab"
                        wire:click="setTab('ouvert')" data-bs-toggle="tab" data-bs-target="#ouvert" type="button"
                        role="tab" aria-controls="ouvert"
                        aria-selected="{{ $activeTab === 'ouvert' ? 'true' : 'false' }}">Ouvert</button>
                </li>
            </ul>
            <div class="tab-content chat-list" id="chatTabsContent">
                <div class="tab-pane fade {{ $activeTab === 'general' ? 'show active' : '' }}" id="general"
                    role="tabpanel" aria-labelledby="general-tab">
                    @if ($chats->isEmpty())
                        <p class="text-muted">Aucun chat trouvé.</p>
                    @else
                        @foreach ($chats as $chat)
                            <div wire:click="selectChat({{ $chat->id }})"
                                class="chat-item card mb-2 p-2 cursor-pointer {{ $selectedChat && $selectedChat->id == $chat->id ? 'active' : '' }}">
                                <div class="d-flex align-items-center">
                                    <img src="https://maxskills.tn/{{ $chat->user->path_photo }}" alt="User"
                                        class="avatar rounded-circle me-3" style="width: 40px; height: 40px;">
                                  
                                    <div class="flex-grow-1">
                                        <p class="mb-0" style="font-weight:500">
                                            {{ $chat->user->firstname . ' ' . $chat->user->name }}
                                            @if ($chat->user->status == 1 && $chat->user->is_demo == 0 )
                                                <img src="{{ asset('assets/icon/paye.png') }}" alt="Payé"
                                                    style="position: absolute; top:10px; margin-left:5px;width: 15px; height: 15px; z-index: 1;">
                                            @endif
                                            @if ($chat->messages->where('readed', false)->where('is_admin', false)->count() > 0)
                                                <span class="badge ms-2">
                                                    {{ $chat->messages->where('readed', false)->where('is_admin', false)->count() }}
                                                </span>
                                            @endif
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            @if ($chat->messages->last())
                                                @if (Str::startsWith($chat->messages->last()->message, 'storage'))
                                                    Client a envoyé une attachment
                                                @else
                                                    {{ Str::limit($chat->messages->last()->message, 30) }}
                                                @endif
                                            @else
                                                Aucun message
                                            @endif
                                        </p>
                                        <small
                                            class="text-muted">{{ $chat->messages->max('sent_at') ? $chat->messages->max('sent_at')->diffForHumans() : '' }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="tab-pane fade {{ $activeTab === 'non-lus' ? 'show active' : '' }}" id="non-lus"
                    role="tabpanel" aria-labelledby="non-lus-tab">
                    @if ($chats->isEmpty())
                        <p class="text-muted">Aucun chat trouvé.</p>
                    @else
                        @foreach ($chats as $chat)
                            <div wire:click="selectChat({{ $chat->id }})"
                                class="chat-item card mb-2 p-2 cursor-pointer {{ $selectedChat && $selectedChat->id == $chat->id ? 'active' : '' }}">
                                <div class="d-flex align-items-center">
                                    <img src="https://maxskills.tn/{{ $chat->user->path_photo }}" alt="User"
                                        class="avatar rounded-circle me-2" style="width: 40px; height: 40px;">
                                    <div class="flex-grow-1">
                                        <p class="mb-0">
                                            {{ $chat->user->firstname . ' ' . $chat->user->name }}
                                            @if ($chat->messages->where('readed', false)->where('is_admin', false)->count() > 0)
                                                <span class="badge ms-2">
                                                    {{ $chat->messages->where('readed', false)->where('is_admin', false)->count() }}
                                                </span>
                                            @endif
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            @if ($chat->messages->last())
                                                @if (Str::startsWith($chat->messages->last()->message, 'storage'))
                                                    Client a envoyé une attachment
                                                @else
                                                    {{ Str::limit($chat->messages->last()->message, 30) }}
                                                @endif
                                            @else
                                                Aucun message
                                            @endif
                                        </p>
                                        <small
                                            class="text-muted">{{ $chat->messages->max('sent_at') ? $chat->messages->max('sent_at')->diffForHumans() : '' }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="tab-pane fade {{ $activeTab === 'ouvert' ? 'show active' : '' }}" id="ouvert"
                    role="tabpanel" aria-labelledby="ouvert-tab">
                    @if ($chats->isEmpty())
                        <p class="text-muted">Aucun chat trouvé.</p>
                    @else
                        @foreach ($chats as $chat)
                            <div wire:click="selectChat({{ $chat->id }})"
                                class="chat-item card mb-2 p-2 cursor-pointer {{ $selectedChat && $selectedChat->id == $chat->id ? 'active' : '' }}">
                                <div class="d-flex align-items-center">
                                    <img src="https://maxskills.tn/{{ $chat->user->path_photo }}" alt="User"
                                        class="avatar rounded-circle me-2" style="width: 40px; height: 40px;">
                                    <div class="flex-grow-1">
                                        <p class="mb-0">
                                            {{ $chat->user->firstname . ' ' . $chat->user->name }}
                                            @if ($chat->messages->where('readed', false)->where('is_admin', false)->count() > 0)
                                                <span class="badge ms-2">
                                                    {{ $chat->messages->where('readed', false)->where('is_admin', false)->count() }}
                                                </span>
                                            @endif
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            @if ($chat->messages->last())
                                                {{ Str::limit($chat->messages->last()->message, 30) }}
                                            @else
                                                Aucun message
                                            @endif
                                        </p>
                                        <small
                                            class="text-muted">{{ $chat->messages->max('sent_at') ? $chat->messages->max('sent_at')->diffForHumans() : '' }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="chat-window col-md-8">
            @if ($selectedChat)
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 style="color:black">{{ $selectedChat->user->firstname . ' ' . $selectedChat->user->name }}</h5>
                        <div class="dropdown">
                            <button class="btn btn-link" type="button" id="chatOptions" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="6" r="2" fill="#000000"/>
                                    <circle cx="12" cy="12" r="2" fill="#000000"/>
                                    <circle cx="12" cy="18" r="2" fill="#000000"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="chatOptions">
                                <li>
                               <a class="dropdown-item" 
   href="#" 
   wire:click.prevent="$set('chatIdToDelete', {{ $selectedChat->id }})"
   data-bs-toggle="modal" 
   data-bs-target="#deleteChatModal">
    Supprimer la discussion
</a>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body messages" style="overflow-y: auto; max-height: 500px;" id="chat-messages">
                        @foreach ($selectedChat->messages as $message)
                            <div class="message {{ $message->is_admin ? 'sent' : 'received' }} mb-2 d-flex {{ $message->is_admin ? 'justify-content-start' : 'justify-content-end' }}"
                                data-message-id="{{ $message->id }}">
                                <div class="message-wrapper">
                                    <div
                                        class="d-flex align-items-center gap-2 {{ $message->is_admin ? '' : 'flex-row-reverse' }}">
                                        <img src="https://maxskills.tn/{{ $message->sender->path_photo }}"
                                            alt="Sender" class="message-avatar rounded-circle"
                                            style="width: 30px; height: 30px;">
                                        <span
                                            class="sender-name">{{ $message->sender->firstname . ' ' . $message->sender->name }}</span>
                                    </div>
                                    @if (
                                        $message->type !== 'message' &&
                                            in_array(strtolower(pathinfo($message->message, PATHINFO_EXTENSION)), [
                                                'png',
                                                'jpeg',
                                                'jpg',
                                                'gif',
                                                'avif',
                                                'svg',
                                                'webp',
                                            ]))
                                        <div class="message-content {{ $message->is_admin ? 'bg-secondary-msg-img text-black' : 'bg-secondary-msg-img text-black' }} p-2 rounded-msg position-relative"
                                            style="padding-right: .5rem !important; padding-bottom: .5rem !important; padding-top: .5rem">
                                            <img src="https://maxskills.tn/{{ $message->message }}"
                                                alt="Uploaded Image" style="max-width: 300px; border-radius: 8px;">
                                        </div>
                                          <small class="timestamp">{{ $message->sent_at->format('H:i') }}</small>
                                    @elseif ($message->type !== 'message')
                                        <div class="message-content {{ $message->is_admin ? 'bg-orange text-black' : 'bg-secondary-msg text-black' }} p-2 rounded-msg position-relative"
                                            style="padding-left: 1rem !important; padding-right: .5rem !important; padding-bottom: .5rem !important; padding-top: .5rem !important">
                                            <a href="https://maxskills.tn/{{ $message->message }}"
                                                download="{{ $message->file_name }}" class="file-download-link"
                                                style="color:#000;">
                                                <img src="{{ asset('client/images/download_icon.png') }}"
                                                    style="font-size: 25px;width:35px;height:35px;"> Télécharger
                                                Fichier.{{ pathinfo($message->message, PATHINFO_EXTENSION) }}
                                            </a>
                                        </div>
                                          <small class="timestamp">{{ $message->sent_at->format('H:i') }}</small>
                                    @else
                                        <div class="message-content {{ $message->is_admin ? 'bg-orange text-black' : 'bg-secondary-msg text-black' }} p-2 rounded-msg position-relative"
                                            style="padding-left: 1rem !important; padding-right: .5rem !important; padding-bottom: .5rem !important; padding-top: .5rem !important">
                                             <div style="white-space: pre-wrap;">{{ $message->message }}</div>
                                            
                                        </div>
                                        <small class="timestamp">{{ $message->sent_at->format('H:i') }}</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer" style="margin-bottom: 1rem" x-data="{
    previews: [],
    imageFiles: [],
    otherFiles: [],
    updateImagePreviews(event) {
        this.imageFiles = Array.from(event.target.files);
        this.previews = [...this.imageFiles.map(file => ({
            url: URL.createObjectURL(file),
            type: file.type,
            name: file.name
        })), ...this.otherFiles.map(file => ({
            url: 'https://maxskills.tn/assets/icon/selected-file.png',
            type: file.type,
            name: file.name
        }))];
    },
    updateFilePreviews(event) {
        this.otherFiles = Array.from(event.target.files);
        this.previews = [...this.imageFiles.map(file => ({
            url: URL.createObjectURL(file),
            type: file.type,
            name: file.name
        })), ...this.otherFiles.map(file => ({
            url: 'https://maxskills.tn/assets/icon/selected-file.png',
            type: file.type,
            name: file.name
        }))];
    },
    removePreview(index, isImage) {
        if (isImage) {
            this.imageFiles.splice(index, 1);
            const newFileList = new DataTransfer();
            this.imageFiles.forEach(file => newFileList.items.add(file));
            this.$refs.imageInput.files = newFileList.files;
        } else {
            this.otherFiles.splice(index, 1);
            const newFileList = new DataTransfer();
            this.otherFiles.forEach(file => newFileList.items.add(file));
            this.$refs.fileInput.files = newFileList.files;
        }
        this.previews = [...this.imageFiles.map(file => ({
            url: URL.createObjectURL(file),
            type: file.type,
            name: file.name
        })), ...this.otherFiles.map(file => ({
            url: 'https://maxskills.tn/assets/icon/selected-file.png',
            type: file.type,
            name: file.name
        }))];
    },
    clearPreviews() {
        this.previews = [];
        this.imageFiles = [];
        this.otherFiles = [];
        this.$refs.imageInput.value = null;
        this.$refs.fileInput.value = null;
    },
    openImageInput() {
        this.$refs.imageInput.click();
    },
    openFileInput() {
        this.$refs.fileInput.click();
    }
}">
    <div class="preview-container mb-2">
        <div id="previewArea" class="d-flex flex-row flex-wrap gap-2">
            <template x-for="(preview, index) in previews" :key="preview.url + index">
                <div class="preview-item" style="position: relative;">
                    <template x-if="preview.type.startsWith('image/')">
                        <img :src="preview.url" class="object-cover rounded" style="width: 60px; height: 60px;">
                    </template>
                    <template x-if="!preview.type.startsWith('image/')">
                        <img :src="preview.url" class="object-cover rounded" style="width: 60px; height: 60px;">
                    </template>
                    <span @click="removePreview(index, preview.type.startsWith('image/'))" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px;" aria-label="Supprimer l'aperçu">×</span>
                </div>
            </template>
        </div>
    </div>
    <div class="input-group-container" style="background-color: #F5F5F5; border: 1px solid #e7e6e6; border-radius: 21px; padding: 10px; margin-bottom: 25px; display: flex; flex-direction: column; gap: 10px;">
        <textarea wire:model="newMessage"  class="form-control chat-textarea" placeholder="Tapez un message" style="height: 35px; resize: none; padding: 7px; border: none; font-size: 14px; background: transparent;" 
x-on:keydown.enter="if (!event.shiftKey) { 
        event.preventDefault(); 
        $wire.sendMessageOrFiles({{ $selectedChat->id }});
    }"        x-ref="messageInput"></textarea>
        <div class="icon-group" style="display: flex !important; align-items: center; gap: 10px;">
            <a class="img-btn" type="button" x-on:click="openImageInput" style="background: transparent; border: none; padding: 5px;" aria-label="Envoyer une image">
                <img src="https://maxskills.tn/assets/icon/send-img-2.png" alt="Image" style="width: 25px; height: 25px;">
            </a>
            <a class="file-btn" type="button" x-on:click="openFileInput" style="background: transparent; border: none; padding: 5px;" aria-label="Envoyer un fichier">
                <img src="https://maxskills.tn/assets/icon/send-file-1.png" alt="File" style="width: 25px; height: 25px;">
            </a>
            <a class="send-btn" x-on:click="clearPreviews" wire:click="sendMessageOrFiles({{ $selectedChat->id }})" style="background: transparent; border: none; padding: 5px; margin-left: auto;" aria-label="Envoyer le message">
                <img src="https://maxskills.tn/assets/icon/send-2.png" alt="Send" style="width: 35px; height: 35px; border-radius: 7px;">
            </a>
            <input type="file" id="imageInput" wire:model="files" multiple style="display: none;" accept="image/*" x-ref="imageInput" x-on:change="updateImagePreviews">
            <input type="file" id="fileInput" wire:model="files" multiple style="display: none;" accept=".pdf,.zip,.psd" x-ref="fileInput" x-on:change="updateFilePreviews">
        </div>
    </div>
</div>

<style>
    .chat-textarea:focus {
                            outline: none !important;
                            box-shadow: none !important;
                            border-color: transparent !important;
    }
    .input-group-container:has(.chat-textarea:focus) {
        background-color: #F5F6FA !important;
    }
    .input-group-container {
        transition: background-color 0.3s ease;
    }
    textarea.form-control.chat-textarea::placeholder {
        color: #000000 !important;
    }
    .chat-textarea:focus {
        background-color: transparent !important;
    }
    .preview-item img {
        border: 1px solid #ddd;
        border-radius: 4px;
    }
</style>
  <!-- Confirmation Modal -->
<div class="modal fade" id="deleteChatModal" tabindex="-1" aria-labelledby="deleteChatModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteChatModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette discussion ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                {{-- IMPORTANT : On retire data-bs-dismiss d'ici pour laisser Livewire gérer la fermeture --}}
                <button type="button" class="btn btn-danger" wire:click="deleteChat" id="deleteChatButton">Supprimer</button>
            </div>
        </div>
    </div>
</div>



                </div>
            @else
                <div class="text-center p-5">Sélectionnez un chat pour commencer...</div>
            @endif 
        </div>
    </div>
</div>

