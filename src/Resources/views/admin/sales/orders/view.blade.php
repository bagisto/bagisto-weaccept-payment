@if($order->payment->method == 'weaccept')
    @if ($order->canRefund())
        <a href="{{ route('admin.sales.weaccept.refunds.create', $order->id) }}" class="btn btn-lg btn-primary">
            {{ __('weaccept::app.admin.system.weaccept_refund') }}
        </a>
        @if ($order->canShip())
            @push('scripts')
                    <script>
                    $( ".page-action a:nth-last-child(2)" ).remove();
                    </script>
            @endpush
        @else
            @push('scripts')
                    <script>
                    $( ".page-action a:nth-last-child(1)" ).remove();
                    </script>
            @endpush
        @endif
    @endif
@endif
