export interface Departure {
    realtimeDepartureTime: number;
    plannedDepartureTime: number;
    transportType: string;
    label: string;
    destination: string;
    platform?: string;
    stopPositionNumber?: string;
}
