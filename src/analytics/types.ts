export type AlertMessage = {
  type: "warning" | "info";
  message: string;
};

export type FunnelStage = {
  stage: string;
  count: number;
};

export type SentimentBucket = {
  sentiment: string | null;
  _count: number;
};

export type FeedbackHighlight = {
  id: string;
  text: string;
  sentiment: string | null;
  category: string | null;
  themeCluster: string | null;
  source: string;
  createdAt: string;
};

export type DailySummary = {
  date: string;
  emailSent: number;
  emailDelivered: number;
  emailOpened: number;
  emailClicked: number;
  surveyInvited: number;
  surveyStarted: number;
  surveyCompleted: number;
  feedbackCount: number;
  positiveCount: number;
  neutralCount: number;
  negativeCount: number;
  campaign: { id: string; name: string; type: string } | null;
};

export type TotalsMetric = {
  eventType: string;
  _count: number;
};

export type ApiOverviewResponse = {
  totals: TotalsMetric[];
  funnel: FunnelStage[];
  sentiment: SentimentBucket[];
  daily: DailySummary[];
  alerts: AlertMessage[];
  feedbackHighlights: FeedbackHighlight[];
};

