export function useMatchUrl() {
    const isMatchingUrl = (currentUrl: string, targetUrl: string): boolean => {
        const targetPath = targetUrl.startsWith('http') ? new URL(targetUrl).pathname : targetUrl;
        return currentUrl.startsWith(targetPath);
    };
    return { isMatchingUrl };
}
