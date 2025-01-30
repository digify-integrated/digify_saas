/**
 * Detects and returns the user's browser and device information.
 *
 * @returns {string} A string containing the browser, version, OS, and device type.
 */
export const getDeviceInfo = () => {
    const userAgent = navigator.userAgent;
    const platform = navigator.platform || 'Unknown Platform';

    // Map of OS and devices
    const deviceMap = {
        Android: /Android/i,
        iOS: /iPhone|iPad|iPod/i,
        Windows: /Windows NT/i,
        MacOS: /Mac OS|Macintosh/i,
        Linux: /Linux/i
    };

    // Map of browsers
    const browserMap = {
        Firefox: /Firefox\/([\d.]+)/i,
        Opera: /OPR\/([\d.]+)|Opera\/([\d.]+)/i,
        Chrome: /Chrome\/([\d.]+)/i,
        Safari: /Safari\/([\d.]+)/i,
        'Internet Explorer': /MSIE ([\d.]+)|Trident.*rv:([\d.]+)/i,
        Edge: /Edg\/([\d.]+)/i
    };

    // Detect OS/Device
    const device = Object.entries(deviceMap).find(([, regex]) => regex.test(userAgent))?.[0] || 'Unknown Device';

    // Detect Browser & Version
    const browserEntry = Object.entries(browserMap).find(([, regex]) => userAgent.match(regex));
    const browser = browserEntry ? browserEntry[0] : 'Unknown Browser';
    const browserVersion = browserEntry ? userAgent.match(browserEntry[1])?.[1] || 'Unknown Version' : 'Unknown Version';

    return `${browser} ${browserVersion} - ${device} (${platform})`;
};