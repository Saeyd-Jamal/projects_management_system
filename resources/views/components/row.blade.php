<div class="row shadow py-1 px-2 my-2 mx-0 bg-white rounded">
    <div class="col-12 d-flex align-items-center p-0" id="content">
        <img src="{{asset('imgs/logo.png')}}" alt="" width="25px" height="25px" id="logo">
        <p id="text1" class="m-0">
            اللهم صلي وسلم على نبينا محمد وعلى آله وصحبه أجمعين
        </p>
        <p id="text2" class="hidden m-0">
            اللهم أكتب لنا في الدنيا حسنة وفي الآخرة حسنة وقنا عذاب النار
        </p>
        <p id="text3" class="hidden m-0">
            اللَّهُمَّ إنِّي أَسْأَلُكَ الهُدَى وَالتُّقَى، وَالْعَفَافَ وَالْغِنَى
        </p>
        <p id="text4" class="hidden m-0">
            اللَّهمَّ إنِّي أسألُك من خيرِ ما أُمِرَتْ بِه وأعوذُ بِك من شرِّ ما أُمِرَت بِه
        </p>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var texts = []; // قائمة النصوص
        // ملء قائمة النصوص بجميع العناصر <p> الموجودة داخل #content
        $('#content p').each(function() {
            texts.push(this); // إضافة كل عنصر <p> في القائمة
        });

        var currentIndex = 0; // مؤشر النص الحالي

        // إخفاء جميع النصوص
        $(texts).each(function() {
            $(this).hide();
        });

        $(texts[currentIndex]).show(); //اظهار النص الحالي
        // تغيير النصوص كل ثانية
        setInterval(function() {
            // إخفاء النص الحالي
            $(texts[currentIndex]).fadeOut(500, function() {
                // تحديث المؤشر للنص التالي
                currentIndex = (currentIndex + 1) % texts.length;
                // إظهار النص الجديد
                $(texts[currentIndex]).fadeIn(500);
            });
        }, 3000); // تغيير النص كل 4 ثواني
    });

</script>
@endpush
