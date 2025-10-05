export interface Departure {
    plannedDepartureTime: number;
    realtime: boolean;
    delayInMinutes: number;
    realtimeDepartureTime: number;
    transportType: string;
    label: string;
    divaId: string;
    network: string;
    trainType: string;
    destination: string;
    cancelled: boolean;
    sev: boolean;
    platform?: number;
    platformChanged?: boolean;
    messages: string[];
    infos: {
        message: string;
        type: string;
        network: string;
    }[];
    bannerHash: string;
    occupancy: "HIGH" | "MEDIUM" | "LOW" | "UNKNOWN";
    stationGlobalId: string;
    stopPointGlobalId: string;
    lineId: string;
    tripId?: string;
    tripCode: number;
}
