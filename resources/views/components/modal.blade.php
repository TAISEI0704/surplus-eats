<div class="container">
    <button class="button" data-modal-open="modal-1">
      {{ __('SElECT CATEGORY') }}
    </button>

    <form action="{{ route('products.filter') }}" method="GET">
        @csrf
      <div class="modal" id="modal-1">
        <div class="modal-overlay" data-modal-close="modal-1">
            <div class="modal-container">
            <h2 class="modal-title">{{ __('CATEGORY') }}</h2>
            <div class="modal-content">
                <div class="container px-5 py-24 mx-auto">
                    <div class="flex flex-wrap md:text-left text-center -mb-10 -mx-4">
                      <div class="lg:w-1/6 md:w-1/2 w-full px-4">
                        <h2 class="title-font font-medium text-gray-900 tracking-widest text-sm mb-3">DISHES</h2>
                        <nav class="list-none mb-10 flex flex-wrap">
                          @foreach($categories_1 as $value => $label)
                          <div class="w-1/2">
                            <input type="checkbox" id="category_{{ $value }}" name="category[]" value="{{ $value }}" class="category-checkbox">
                            <label for="category_{{ $value }}" class="text-gray-600 hover:text-gray-800">{{ $label }}</label>
                          </div>
                          @endforeach
                        </nav>
                      </div>
                      <div class="lg:w-1/6 md:w-1/2 w-full px-4">
                        <h2 class="title-font font-medium text-gray-900 tracking-widest text-sm mb-3">INGREDIENTS</h2>
                        <nav class="list-none mb-10 flex flex-wrap">
                          @foreach($categories_2 as $value => $label)
                          <div class="w-1/2">
                            <input type="checkbox" id="category_{{ $value }}" name="category[]" value="{{ $value }}" class="category-checkbox">
                            <label for="category_{{ $value }}" class="text-gray-600 hover:text-gray-800">{{ $label }}</label>
                          </div>
                          @endforeach
                        </nav>
                      </div>
                    </div>
                  </div>                  
            </div>
            <div class="modal-footer">
                <button class="button" data-modal-close="modal-1" data-close-type="close">{{ __('Clear') }}</button>
                <button type="submit" class="button" data-modal-close="modal-1" data-close-type="ok">{{ __('Search') }}</button>
            </div>
            </div>
        </div>
      </div>
    </form>
  </div>

  <style>
    .container {
        padding: 1rem;
    }

    .modal {
        z-index: 9999;
    }

    .modal-overlay {
        align-items: center;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        transition: opacity 200ms linear;
        z-index: 9999;
    }

    .modal.is-open .modal-overlay {
        opacity: 1;
        pointer-events: auto;
    }

    .modal-container {
        background-color: #fff;
        border-radius: 6px;
        min-width: 300px;
        max-height: 90vh;
        padding: 1.5rem;
        transform: translateY(24px);
        transition: transform 300ms ease-in-out;
        width: 90%;
    }

    .modal.is-open .modal-container {
        transform: translateY(0);
    }

    .modal-title {
        border-bottom: 1px solid #dedede;
        font-size: 1.25rem;
        margin: 0 0 1.5rem 0;
        padding-bottom: 0.5rem;
    }

    .modal-footer {
        margin-top: 1.5rem;
        text-align: right;
    }

    .button {
        background-color: #71c9ce;
        border: 0;
        border-radius: 6px;
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.12);
        color: #fff;
        cursor: pointer;
        line-height: 1;
        outline: 0;
        padding: 0.75rem 1rem;
    }
  </style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const elem = document.getElementById('modal-1');
        new Modal(elem);
    });

    /**
     * モーダルウィンドウ
     * @property {HTMLElement} modal モーダル要素
     * @property {NodeList} openers モーダルを開く要素
     * @property {NodeList} closers モーダルを閉じる要素
     */
    function Modal(modal) {
        this.modal = modal;
        const id = this.modal.id;
        this.openers = document.querySelectorAll('[data-modal-open="' + id + '"]');
        this.closers = this.modal.querySelectorAll('[data-modal-close]');

        this.handleOpen();
        this.handleClose();
    }

    /**
 * 開くボタンのイベント登録
 */
Modal.prototype.handleOpen = function() {
  if (this.openers.length === 0) {
    return false;
  }

  this.openers.forEach(opener => {
    opener.addEventListener('click', this.open.bind(this));
  });
};

/**
 * 閉じるボタンのイベント登録
 */
Modal.prototype.handleClose = function() {
  if (this.closers.length === 0) {
    return false;
  }

  this.closers.forEach(closer => {
    closer.addEventListener('click', this.close.bind(this));
  });
};

/**
 * モーダルを開く
 */
Modal.prototype.open = function(event) {
  event.stopPropagation(); // ラジオボタンのクリックイベントをキャンセル
  this.modal.classList.add('is-open');
};

/**
 * モーダルを閉じる
 */
Modal.prototype.close = function(event) {
  event.stopPropagation(); // ラジオボタンのクリックイベントをキャンセル
  const closeButton = event.target;
  const closeType = closeButton.getAttribute('data-close-type');

// チェックボックスのクリックイベントをキャンセルする
const checkboxes = document.querySelectorAll('.category-checkbox');
checkboxes.forEach(checkbox => {
  checkbox.addEventListener('click', (event) => {
    event.stopPropagation();
  });
});  

  // CLOSEボタンをクリックした場合のみモーダルを閉じる
  if (closeType === 'close') {
    this.modal.classList.remove('is-open');
  }
};
</script>