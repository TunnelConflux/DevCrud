@push("header_scripts")
	<!-- DataTables -->
	<link rel="stylesheet"
		  href="{{ asset("backend_assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.css") }}" />
@endpush

@push('footer_scripts')
	<!-- DataTables -->
	<script src="{{ asset("backend_assets/bower_components/datatables.net/js/jquery.dataTables.min.js") }}"></script>
	<script src="{{ asset("backend_assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.js") }}"></script>
@endpush

@push("footer_scripts")
	<script type="text/javascript">
        $(function () {
            $('table').DataTable({
                'paging': true,
                'ordering': true,
                'info': true,
                "lengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]]
            });
        });
	</script>
@endpush