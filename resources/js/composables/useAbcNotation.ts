import { Arrangement } from '@/types';

export function useAbcNotation(arrangement: Arrangement) {
    const notesLines = JSON.parse(arrangement.notes_lines || '[]') as string[];
    const wordsLines = JSON.parse(arrangement.words_lines || '[]') as string[];
    const tuneBodyLines = JSON.parse(arrangement.tune_body_lines || '[]') as string[];
    const informationFields = [
        `X: ${arrangement.id}`,
        arrangement.tune.title && `T: ${arrangement.tune.title}`,
        arrangement.tune.composer?.name && `C: ${arrangement.tune.composer.name}`,
        arrangement.rhythm && `R: ${arrangement.rhythm.name}`,
        arrangement.meter && `M: ${arrangement.meter.name}`,
        arrangement.tempo && `Q: ${arrangement.tempo}`,
        arrangement.tune.origin && `O: ${arrangement.tune.origin.name}`,
        arrangement.parts && `P: ${arrangement.parts}`,
        arrangement.transcription && `Z: ${arrangement.transcription}`,
    ]
        .filter(Boolean)
        .join('\n');

    const informationFieldsWithoutTranscription = [
        `X: ${arrangement.id}`,
        arrangement.tune.title && `T: ${arrangement.tune.title}`,
        arrangement.tune.composer?.name && `C: ${arrangement.tune.composer.name}`,
        arrangement.rhythm && `R: ${arrangement.rhythm.name}`,
        arrangement.meter && `M: ${arrangement.meter.name}`,
        arrangement.tempo && `Q: ${arrangement.tempo}`,
        arrangement.parts && `P: ${arrangement.parts}`,
    ]
        .filter(Boolean)
        .join('\n');

    const notesField = notesLines.map((line) => `N: ${line}`).join('\n');

    const wordsField = wordsLines.map((line) => `W: ${line}`).join('\n');

    const keyField = `K: ${arrangement.key}`;

    const tuneBody = tuneBodyLines.join('\n');

    const abcNotation = [informationFields, notesField, wordsField, keyField, tuneBody].filter((part) => part).join('\n');

    const abcNotationWithoutWordsAndNotesAndTranscription = [informationFieldsWithoutTranscription, keyField, tuneBody]
        .filter((part) => part)
        .join('\n');

    return {
        abcNotationWithoutWordsAndNotesAndTranscription,
        abcNotation,
        tuneBody,
        words: wordsLines.join('\n'),
        notes: notesLines.join('\n'),
    };
}
