<script>
export default {
    name: 'DynamicButton',
    props: {
        href: {
            type: String,
            default: '#'
        },
    },
}
</script>

<template>
    <a :href="href"
        class="inline-flex gap-5 items-center px-7 py-4 md:text-[1em] text-[0.9em] font-semibold tracking-wide text-white transition duration-150 ease-in-out bg-[#0063F2] border border-transparent rounded-full hover:bg-[#003B91] focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-[#003B91]">
        <slot />
        <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="39" height="39" fill="none">
                <rect width="39" height="38.049" fill="#fff" rx="19.024"></rect>
                <path fill="#0063f2" fill-rule="evenodd" class="size-4"
                    d="M17.737 12.393a1.003 1.003 0 1 0-.274 1.987l6.776.934-11.692 8.336a1.003 1.003 0 1 0 1.164 1.633l11.564-8.244-1.284 6.496a1.003 1.003 0 1 0 1.968.39l1.794-9.08a1.003 1.003 0 0 0-.847-1.189z"
                    clip-rule="evenodd"></path>
            </svg>
        </span>
    </a>
</template>
