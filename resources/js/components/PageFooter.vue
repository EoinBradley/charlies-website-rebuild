<script setup>
    import moment from "moment";
</script>

<template>
    <footer>
        <div class="w-full text-center text-gray-500 text-sm py-3">
            © {{ moment().format('YYYY') }}: All rights reserved<br />
            Site designed and maintained by <a href="https://eoinbradley.com/" target="_blank">Eoin Bradley</a>
        </div>
    </footer>
</template>
