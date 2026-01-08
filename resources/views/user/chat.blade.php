@extends('user.layout.layout')
@section('content')
    <div class="mt-5">
        <div class="row">
            <div class="col-md-3">
                <div class="user-lits">
                    <div class="d-flex justify-content-between">
                        <h4>Đoạn chat</h4>
                    </div>
                    <div id="user-list" class="main-user-list"></div>
                </div>
            </div>
            <div class="col-md-9 main-chat">
                <div id="chat-area">
                    <!-- Mặc định hiển thị thông báo này -->
                    <div id="default-message" class="text-center mt-5">
                        <h5>Vui lòng chọn một người dùng từ danh sách để bắt đầu trò chuyện</h5>
                    </div>

                    <!-- Khu vực chat sẽ hiển thị khi đã chọn user -->
                    <div id="chat-container" style="display: none;">
                        <h5 id="chat-with"></h5>
                        <div id="messages"></div>
                        <form id="chat-form">
                            <input type="text" name="message" placeholder="Nhập tin nhắn..." required autocomplete="off">
                            <button type="submit">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>



    <script>
        window.AUTH_USER = @json(auth()->user());
    </script>
    @vite(['resources/js/components/chat/chat.js'])

@endsection
