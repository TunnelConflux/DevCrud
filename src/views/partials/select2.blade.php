@push("css")
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset("/plugins/select2/select2.min.css") }}">
@endpush

@push('js')
    <!-- Select2 -->
    <script src="{{ asset("/plugins/select2/select2.full.min.js") }}"></script>
@endpush

@push('script')
    <script type="text/javascript">
        window.addEventListener("DOMContentLoaded", function(){
            jQuery("select").each(function(){
                $this = jQuery(this);
                
                if($this.attr('data-reorder')){
                    $this.on('select2:select', function(e){
                        var elm = e.params.data.element;
                        $elm = jQuery(elm);
                        $t = jQuery(this);
                        $t.append($elm);
                        $t.trigger('change.select2');
                    });
                }
                
                $this.select2({
                    placeholder: 'Select an Option',
                    allowClear: true,
                    width: '100%',
                });
            });
        });
    </script>
@endpush