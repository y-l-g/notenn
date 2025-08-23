<script setup lang="ts">
import AbcExample from '@/components/app/abcjs/AbcExample.vue';
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
defineOptions({ layout: PersistentLayout })
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('ABC Cheat Sheet'),
        href: '/abc',
    },
];


const examples = [
    {
        "title": t('Notes / pitches'),
        "code": "C, D, E, F, | G, A, B, C | D E F G | A B c d | e f g a | b c' d' e' | f' g' a' b' |]"
    },
    {
        "title": t("Note lengths"),
        "code": "A/4 A/2 A/ A A2 A3 A4 A6 A7 A8 A12 A16 |]"
    },
    {
        "title": t("Beams"),
        "code": "A B c d AB cd | ABcd ABc2 | ABcdABcd |]"
    },
    {
        "title": t("Bar lines"),
        "code": "[| A4 A4 | A4 A4 || A4 A4 | A4 A4 |]" +
            "|: A4 A4 | A4 A4 :: A4 A4 | A4 A4 ::" +
            "A4 A4 | A4 A4 | 1 A4 A4 :| 2 A4 A4 | A4 A4 |]"
    },
    {
        "title": t("Broken rhythm markers"),
        "code": "A>A A2>A2 | A<A A2<A2 | A>>A A2>>>A2 | A<<A A2<<<A2 |]"
    },
    {
        "title": t("Tuplets"),
        "code": "(2AB (3ABA (4ABAB (5ABABA (6ABABAB (7ABABABA|]"
    },
    {
        "title": t("Ties and slurs"),
        "code": "(AA) (A(A)A) ((AA)A) (A|A) A-A A-A-A A2-|A4|]"
    },
    {
        "title": t("Chord symbols"),
        "code": "| \"C\" z2 \"G\" z | \"Am\" z \"F\" z |"
    },
    {
        "title": t("Accidentals"),
        "code": "__A _A =A ^A ^^A |]"
    },
    {
        "title": t("Accents"),
        "code": "~A ~c .A .c vA vc uA uc|]"
    },
    {
        "title": t("Grace notes"),
        "code": "{g}A3 A{g}AA|{gAGAG}A3 {g}A{d}A{e}A|]"
    },
    {
        "title": t("Chords"),
        "code": "[CEGc] [C2G2] [CE][DF] | [D2F2][EG][FA] [A4d4]|]"
    },
    {
        "title": t("Dynamics"),
        "code": "!pp!A !p!B  !mp!D !mf!E !f!F !ff!G !fff!A|]"
    },
    {
        "title": t("Ornaments"),
        "code": "!trill!A !roll!B !turn!C !mordent!D !pralltriller!E !uppermordent!F|]"
    },
    {
        "title": t("Multi-voice"),
        "code": "[V:1] | A B c d |] \n[V:2] | A, B, C D |]"
    },
    {
        "title": t("Lyrics"),
        "code": "A A A A | A A A A |\nw:syl-la-ble, syl- la- ble"
    },
]

</script>

<template>

    <Head :title="t('ABC Cheat Sheet')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Heading>{{ t('ABC Cheat Sheet') }}</Heading>
        <p>For exhaustive documentation, please refer to <a class="underline"
                href="https://abcnotation.com/wiki/abc:standard:v2.2">abc
                notation standard</a></p>
        <div class="space-y-10">
            <AbcExample v-for="example in examples" :key=example.title :abc-string="example.code"
                :title="t(example.title)" />
        </div>
    </AppLayout>
</template>
