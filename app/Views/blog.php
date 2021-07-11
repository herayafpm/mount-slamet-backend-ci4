<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Blog</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
</head>

<body>
    <div id="summernote"></div>
    <div class="mx-2 mt-2">
        <button class="btn btn-success btn-block" onclick="simpan()">Simpan</button>
    </div>
    <script>
        function simpan() {
            var formData = new FormData()
            formData.append("blog_isi", $('#summernote').summernote('code'))
            $.ajax({
                data: formData,
                type: "POST",
                url: "<?= base_url('blog/update_blog') ?>",
                cache: false,
                contentType: false,
                processData: false,
                success: function(res) {
                    res = JSON.parse(res)
                    if (res.status) {
                        window.location.reload()
                    } else {
                        alert("ada yang salah")
                    }
                }
            });
        }
        $(function() {
            $('#summernote').summernote({
                width: "100%",
                height: 500,
                callbacks: {
                    onImageUpload: function(files, editor, welEditable) {
                        console.log(files)
                        var formData = new FormData()
                        formData.append("file", files[0])
                        $.ajax({
                            data: formData,
                            type: "POST",
                            url: "<?= base_url('blog/upload_file') ?>",
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(url) {
                                $('#summernote').summernote('insertImage', url, "");
                            }
                        });
                    }
                }
            });
            $('#summernote').summernote('code', `<?= $blog['blog_isi'] ?? "" ?>`)
        })
    </script>
</body>

</html>